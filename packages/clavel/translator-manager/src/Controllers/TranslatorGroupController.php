<?php namespace Clavel\TranslatorManager\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Clavel\TranslatorManager\Manager;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\AdminController;
use Clavel\TranslatorManager\Models\Translation;
use Clavel\TranslatorManager\Models\TranslationGroup;
use Tanmuhittin\LaravelGoogleTranslate\Commands\TranslateFilesCommand;

class TranslatorGroupController extends AdminController
{
    protected $page_title_icon = '<i class="fa fa-language" aria-hidden="true"></i>';

    /** @var Manager */
    protected $manager;

    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
        parent::__construct();

        $this->access_permission = 'admin-translator';
    }

    /**
     * Mostramos la lista de textos en los diferentes idiomas de un fichero de recursos
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Si no tiene permisos para ver el listado lo echa.
        if (!auth()->user()->can('admin-translator-list')) {
            app()->abort(403);
        }

        $group = $request->input('group');

        $page_title = trans("translator-manager::translator/admin_lang.grupo")." - ".$group;

        $locales = $this->manager->getLocales();

        $numChanged = Translation::where('group', $group)->where('status', Translation::STATUS_CHANGED)->count();
        $allTranslations = Translation::where('group', $group)->orderBy('key', 'asc')->get();
        $numTranslations = count($allTranslations);
        $translations = [];
        foreach ($allTranslations as $translation) {
            $translations[$translation->key][$translation->locale] = $translation;
        }

        return view("translator-manager::translator.admin_group_index", compact(
            'page_title'
        ))
            ->with('translations', $translations)
            ->with('locales', $locales)
            ->with('group', $group)
            ->with('numTranslations', $numTranslations)
            ->with('numChanged', $numChanged)
            ->with('deleteEnabled', $this->manager->getConfig('delete_enabled'))
            ->with('page_title_icon', $this->page_title_icon);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        if (!Auth::user()->can('admin-translator-create')) {
            abort(404);
        }

        $group = $request->input('group', '');
        $keys = explode("\n", request()->get('keys'));
        $newKeys = [];
        foreach ($keys as $key) {
            $key = Str::slug(trim($key));
            if ($group && $key) {
                $this->manager->missingKey('*', $group, $key);
                $newKeys[] = $key;
            }
        }

        $newKeysConfirmed = Translation::distinct()->select('key')->whereIn('key', $newKeys)->get()->toArray();

        return Response::json(array(
            'success' => true,
            'keys' => $newKeysConfirmed
        ));
    }

    /**
     * Actualizamos en bbdd la información de la clave para el valor del idioma
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request)
    {
        // Si no tiene permisos para ver el listado lo echa.
        if (!auth()->user()->can('admin-translator-update')) {
            app()->abort(403);
        }

        $group = $request->input('group', '');
        if (!in_array($group, $this->manager->getConfig('exclude_groups'))) {
            $key = request()->input('key');
            $value = request()->input('content');
            $locale = request()->input('locale');
            $translation = Translation::firstOrNew([
                'locale' => $locale,
                'group' => $group,
                'key' => $key,
            ]);
            $translation->value = (string) $value ?: null;
            $translation->status = Translation::STATUS_CHANGED;
            $translation->save();
            return Response::json(array(
                'success' => true
            ));
        }
        return Response::json(array(
            'success' => false
        ));
    }

    /**
     * Borra por completo una linea de traducciones en la bbdd para todos los idiomas
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function destroy(Request $request)
    {
        // Si no tiene permisos para ver el listado lo echa.
        if (!auth()->user()->can('admin-translator-delete')) {
            app()->abort(403);
        }

        $group = $request->input('group', '');
        if (!in_array($group, $this->manager->getConfig('exclude_groups'))
            && $this->manager->getConfig('delete_enabled')) {
            $key = request()->input('key');
            Translation::where('group', $group)->where('key', $key)->delete();
            return Response::json(array(
                'success' => true,
                'msg' => trans('elearning::alumnos/admin_lang.registro_borrado')
            ));
        }

        return Response::json(array(
            'success' => false,
            'msg' => trans('elearning::alumnos/admin_lang.registro_borrado')
        ));
    }

    /**
     * Publica las traducciones de un modulo en concreto en las carpetas correspondientes
     *
     * @param  Illuminate\Http\Request $request
     * @return void
     */
    public function publish(Request $request)
    {
        // Si no tiene permisos para ver el listado lo echa.
        if (!auth()->user()->can('admin-translator-create')) {
            app()->abort(403);
        }

        $groupName = $request->input('group', '');
        $group = Translation::ofTranslatedGroup($groupName)
            ->first();
        if (empty($group)) {
            return [
                'status' => false,
                'msg' => trans('translator-manager::translator/admin_lang.publish_msg_ko')
            ];
        }
        $groupTranslation = TranslationGroup::where('group', $group->group)->first();
        if (empty($groupTranslation)) {
            return [
                'status' => false,
                'msg' => trans('translator-manager::translator/admin_lang.publish_msg_ko')
            ];
        }

        if ($group->custom) {
            $this->manager->exportTranslationsCustom($group->group, $groupTranslation->path);
        } else {
            $this->manager->exportTranslations($group->group, false);
        }

        return [
            'status' => true,
            'msg' => trans('translator-manager::translator/admin_lang.publish_msg_ok')
        ];
    }

    /**
     * Publica la traduccion individual de un modulo en concreto en la carpeta correspondiente al idioma
     *
     * @param  Illuminate\Http\Request $request
     * @return void
     */
    public function publishLocale(Request $request)
    {
        // Si no tiene permisos para ver el listado lo echa.
        if (!auth()->user()->can('admin-translator-create')) {
            app()->abort(403);
        }

        $locale = $request->input('locale', '');

        if (empty($locale)) {
            return [
                'status' => false,
                'msg' => trans('translator-manager::translator/admin_lang.publish_msg_ko')
            ];
        }

        $groupName = $request->input('group', '');
        $group = Translation::ofTranslatedGroup($groupName)
            ->first();
        if (empty($group)) {
            return [
                'status' => false,
                'msg' => trans('translator-manager::translator/admin_lang.publish_msg_ko')
            ];
        }
        $groupTranslation = TranslationGroup::where('group', $group->group)->first();
        if (empty($groupTranslation)) {
            return [
                'status' => false,
                'msg' => trans('translator-manager::translator/admin_lang.publish_msg_ko')
            ];
        }

        if ($group->custom) {
            $this->manager->exportTranslationsCustom($group->group, $groupTranslation->path, $locale);
        } else {
            $this->manager->exportTranslations($group->group, false, $locale);
        }

        return [
            'status' => true,
            'msg' => trans('translator-manager::translator/admin_lang.publish_msg_ok')
        ];
    }

    /**
     * Autotraducimos todos los textos a partir del idioma origen hacia el idioma destino
     *
     * @param  mixed $request
     * @return void
     */
    public function autoTranslate(Request $request)
    {
        // Leemos los idiomas disponibles
        $locales = $this->manager->getLocales();
        // Miramos el idioma solicitado
        $newLocale = str_replace([], '-', trim($request->input('new-locale')));

        if ($request->has('with-translations') && $request->has('base-locale') &&
            in_array($request->input('base-locale'), $locales) &&
            $request->has('file') &&
            in_array($newLocale, $locales)) {
            $base_locale = $request->get('base-locale');
            $group = $request->get('file');
            $forced = $request->get('forced', false);
            $base_strings = Translation::where('group', $group)->where('locale', $base_locale)->get();
            foreach ($base_strings as $base_string) {
                $translated = Translation::where('group', $group)
                    ->where('locale', $newLocale)->where('key', $base_string->key);
                if (!$forced && $translated->exists() && $translated->whereNotNull('value')->exists()) {
                    // Translation already exists. Skip
                    continue;
                }

                if (!$translated->exists()) {
                    $translated = new Translation();
                    $translated->group = $group;
                    $translated->locale = $newLocale;
                    $translated->key = $base_string->key;
                    $translated->custom = $base_string->custom;
                } else {
                    $translated = $translated->first();
                }

                // Llamamos al servicio de traducción
                $translated_text = TranslateFilesCommand::translate($base_locale, $newLocale, $base_string->value);

                $translated->value = $translated_text;
                $translated->save();
            }
        }

        return [
            'status' => true,
            'msg' => trans('translator-manager::translator/admin_lang.publish_msg_ok')
        ];
    }
}
