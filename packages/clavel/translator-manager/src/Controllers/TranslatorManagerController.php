<?php

namespace Clavel\TranslatorManager\Controllers;

use App\Http\Controllers\AdminController;
use Clavel\TranslatorManager\Manager;
use Clavel\TranslatorManager\Models\Translation;
use DirectoryIterator;
use FilesystemIterator;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Tanmuhittin\LaravelGoogleTranslate\Commands\TranslateFilesCommand;

class TranslatorManagerController extends AdminController
{
    protected $page_title_icon = '<i class="fa fa-language" aria-hidden="true"></i>';

    /** @var \Clavel\TranslatorManager\Manager  */
    protected $manager;

    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
        parent::__construct();

        $this->access_permission = 'admin-translator';
    }

    /**
     * Muestra el listado de módulos cargados y permite la creación de idiomas a crear.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Si no tiene permisos para ver el listado lo echa.
        if (!auth()->user()->can('admin-translator-list')) {
            app()->abort(403);
        }

        $page_title = trans("translator-manager::translator/admin_lang.traducciones");

        // Leemos los idiomas que hay dentro de resources/lang
        $locales = $this->manager->getLocales();

        // Leemos los grupos de traducciones
        $groups = Translation::groupBy('group');
        $excludedGroups = $this->manager->getConfig('exclude_groups');
        if ($excludedGroups) {
            $groups->whereNotIn('group', $excludedGroups);
        }
        $groups = $groups->select('group')->orderBy('group')->get()->pluck('group', 'group');
        if ($groups instanceof Collection) {
            $groups = $groups->all();
        }

        return view("translator-manager::translator.admin_index", compact(
            'page_title',
            'locales',
            'groups'
        ))
            ->with('page_title_icon', $this->page_title_icon);
    }

    /**
     * Crea el idioma solicitado si no existe previamente añadiendo la carpeta del idioma en
     * /resources/lang
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Si no tiene permisos para ver el listado lo echa.
        if (!auth()->user()->can('admin-translator-create')) {
            app()->abort(403);
        }

        // Obtenemos los idiomas del sistema
        $locales = $this->manager->getLocales();
        // Obtenemos el nuevo idioma solicitado
        $newLocale = str_replace([], '-', trim($request->input('new-locale')));
        // Verificamos que el idioma no existia previamente
        if (!$newLocale || in_array($newLocale, $locales)) {
            return redirect()->to('admin/translator')
                ->with('error', trans('translator-manager::translator/admin_lang.create_ko'));
        }
        // Añadimos el nuevo idioma
        $this->manager->addLocale($newLocale);

        return redirect()->to('admin/translator')
            ->with('success', trans('translator-manager::translator/admin_lang.save_ok'));
    }

    /**
     * Eliminamos el idioma del sistema
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Si no tiene permisos para modificar lo echamos
        if (!auth()->user()->can('admin-translator-delete')) {
            app()->abort(404);
        }

        $this->manager->removeLocale($id);

        return redirect()->to('admin/translator')
            ->with('success', trans('translator-manager::translator/admin_lang.delete_ok'));
    }

    /**
     * Importamos los idiomas de:
     * /resources/lang
     * /app/Modules/
     * /packages/
     *
     * @param  mixed $request
     * @return void
     */
    public function import(Request $request)
    {
        // Si no tiene permisos para ver el listado lo echa.
        if (!auth()->user()->can('admin-translator-create')) {
            app()->abort(403);
        }

        // Vemos si queremos borrar los contenidos previos
        $replace = $request->get('replace_id', false);

        // importamos los idiomas de la carpeta /resources/lang
        $counter = $this->manager->importTranslations($replace);

        // Obtenemos los directorios de idiomas de los modulos y de los paquetes
        $directories = [];
        $this->recursiveDirectoryIterator(
            base_path().DIRECTORY_SEPARATOR."packages",
            "Translations",
            $directories
        );
        $this->recursiveDirectoryIterator(
            app_path().DIRECTORY_SEPARATOR."Modules",
            "Translations",
            $directories
        );

        // Procesamos los ficheros de idiomas de los modulos y de los paquetes
        foreach ($directories as $directory) {
            // Debemos evitar los directorios de stubs ya que contienen elementos que no queremos traducir
            if (strpos($directory, 'stubs') === false) {
                $counter += $this->manager->importTranslationsCustom($replace, $directory);
            }
        }

        return [
            'status' => 'ok',
            'counter' => (int) $counter,
            'msg' => trans('translator-manager::translator/admin_lang.import_msg_ok', [ 'counter' => $counter ])
        ];
    }

    /**
     * @param string $directory
     * @param string $directoryName
     * @param array $directories
     * @return void
     */
    private function recursiveDirectoryIterator($directory, $directoryName, &$directories)
    {
        $iterator = new \DirectoryIterator($directory);

        foreach ($iterator as $info) {
            if (!$info->isFile() && !$info->isDot()) {
                if ($info->__toString() == $directoryName) {
                    $directories [] = $info->getPathname();
                } else {
                    $this->recursiveDirectoryIterator(
                        $directory.DIRECTORY_SEPARATOR.$info->__toString(),
                        $directoryName,
                        $directories
                    );
                }
            }
        }
        return;
    }

    public function find(Request $request)
    {
        // Si no tiene permisos para ver el listado lo echa.
        if (!auth()->user()->can('admin-translator-create')) {
            app()->abort(403);
        }

        $numFound = $this->manager->findTranslations();

        return [
            'status' => 'ok',
            'counter' => (int) $numFound,
            'msg' => trans('translator-manager::translator/admin_lang.find_msg_ok', [ 'counter' => $numFound ])
        ];
    }

    public function publish(Request $request)
    {
        // Si no tiene permisos para ver el listado lo echa.
        if (!auth()->user()->can('admin-translator-create')) {
            app()->abort(403);
        }

        // * Todos los grupos
        // grupo
        $this->manager->exportTranslations("*", false);
        $this->manager->exportTranslationsCustom("*");
        return [
            'status' => 'ok',
            'msg' => trans('translator-manager::translator/admin_lang.publish_msg_ok')
        ];
    }

    public function getIndex()
    {
        $group = 'error/404';
        //$group = null;

        // Si no tiene permisos para ver el listado lo echa.
        if (!auth()->user()->can('admin-translator-list')) {
            app()->abort(403);
        }

        $page_title = trans("translator-manager::translator/admin_lang.traducciones");

        $locales = $this->manager->getLocales();
        $groups = Translation::groupBy('group');
        $excludedGroups = $this->manager->getConfig('exclude_groups');
        if ($excludedGroups) {
            $groups->whereNotIn('group', $excludedGroups);
        }
        $groups = $groups->select('group')->orderBy('group')->get()->pluck('group', 'group');
        if ($groups instanceof Collection) {
            $groups = $groups->all();
        }
        $groups = [''=>'Choose a group'] + $groups;
        $numChanged = Translation::where('group', $group)->where('status', Translation::STATUS_CHANGED)->count();
        $allTranslations = Translation::where('group', $group)->orderBy('key', 'asc')->get();
        $numTranslations = count($allTranslations);
        $translations = [];
        foreach ($allTranslations as $translation) {
            $translations[$translation->key][$translation->locale] = $translation;
        }

        return view("translator-manager::translator.admin_index_old", compact('page_title'))
            ->with('translations', $translations)
            ->with('locales', $locales)
            ->with('groups', $groups)
            ->with('group', $group)
            ->with('numTranslations', $numTranslations)
            ->with('numChanged', $numChanged)
            ->with(
                'editUrl',
                action(
                    '
                \Clavel\TranslatorManager\Controllers\TranslatorManagerController@update',
                    [$group]
                )
            )
            ->with('deleteEnabled', $this->manager->getConfig('delete_enabled'))
            ->with('page_title_icon', $this->page_title_icon);
    }

    public function getView($group = null)
    {
        return $this->index($group);
    }

    protected function loadLocales()
    {
        //Set the default locale as the first one.
        $locales = Translation::groupBy('locale')
            ->select('locale')
            ->get()
            ->pluck('locale');
        if ($locales instanceof Collection) {
            $locales = $locales->all();
        }
        $locales = array_merge([config('app.locale')], $locales);
        return array_unique($locales);
    }

    public function postAdd($group = null)
    {
        $keys = explode("\n", request()->get('keys'));
        foreach ($keys as $key) {
            $key = trim($key);
            if ($group && $key) {
                $this->manager->missingKey('*', $group, $key);
            }
        }
        return redirect()->back();
    }

    public function postEdit($group = null)
    {
        if (!in_array($group, $this->manager->getConfig('exclude_groups'))) {
            $name = request()->get('name');
            $value = request()->get('value');
            list($locale, $key) = explode('|', $name, 2);
            $translation = Translation::firstOrNew([
                'locale' => $locale,
                'group' => $group,
                'key' => $key,
            ]);
            $translation->value = (string) $value ?: null;
            $translation->status = Translation::STATUS_CHANGED;
            $translation->save();
            return array('status' => 'ok');
        }
    }

    public function postDelete($key, $group = null)
    {
        if (!in_array($group, $this->manager->getConfig('exclude_groups')) &&
            $this->manager->getConfig('delete_enabled')) {
            Translation::where('group', $group)->where('key', $key)->delete();
            return ['status' => 'ok'];
        }
    }

    public function postImport(Request $request)
    {
        $replace = $request->get('replace', false);
        $counter = $this->manager->importTranslations($replace);
        return ['status' => true, 'counter' => $counter];
    }

    public function postFind()
    {
        $numFound = $this->manager->findTranslations();
        return [
            'status' => 'ok',
            'counter' => (int) $numFound,
            'msg' => trans('import_msg_ok')
        ];
    }

    public function postPublish($group = null)
    {
        $json = false;
        if ($group === '_json') {
            $json = true;
        }
        $this->manager->exportTranslations($group, $json);
        return ['status' => 'ok'];
    }


    public function postAddGroup(Request $request)
    {
        $group = str_replace(".", '', $request->input('new-group'));
        if ($group) {
            return redirect()
                ->action('\Clavel\TranslatorManager\Controllers\TranslatorManagerController@getView', $group);
        } else {
            return redirect()->back();
        }
    }

    public function postAddLocale(Request $request)
    {
        $locales = $this->manager->getLocales();
        $newLocale = str_replace([], '-', trim($request->input('new-locale')));
        if (!$newLocale || in_array($newLocale, $locales)) {
            return redirect()->back();
        }
        $this->manager->addLocale($newLocale);
        return redirect()->back();
    }

    public function postRemoveLocale(Request $request)
    {
        foreach ($request->input('remove-locale', []) as $locale => $val) {
            $this->manager->removeLocale($locale);
        }
        return redirect()->back();
    }

    public function postTranslateMissing(Request $request)
    {
        $locales = $this->manager->getLocales();
        $newLocale = str_replace([], '-', trim($request->input('new-locale')));
        if ($request->has('with-translations') &&
            $request->has('base-locale') &&
            in_array($request->input('base-locale'), $locales) &&
            $request->has('file') &&
            in_array($newLocale, $locales)
        ) {
            $base_locale = $request->get('base-locale');
            $group = $request->get('file');
            $base_strings = Translation::where('group', $group)->where('locale', $base_locale)->get();
            foreach ($base_strings as $base_string) {
                $base_query = Translation::where('group', $group)
                    ->where('locale', $newLocale)->where('key', $base_string->key);
                if ($base_query->exists() && $base_query->whereNotNull('value')->exists()) {
                    // Translation already exists. Skip
                    continue;
                }
                $translated_text = TranslateFilesCommand::translate($base_locale, $newLocale, $base_string->value);
                request()->replace([
                    'value' => $translated_text,
                    'name' => $newLocale . '|' . $base_string->key,
                ]);
                app()->call(
                    '\Clavel\TranslatorManager\Controllers\TranslatorManagerController@postEdit',
                    [
                        'group' => $group
                    ]
                );
            }
            return redirect()->back();
        }
        return redirect()->back();
    }
}
