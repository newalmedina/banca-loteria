<?php

namespace Clavel\Basic\Controllers\Pages;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Clavel\Basic\Models\Page;
use App\Models\PermissionsTree;
use App\Services\LanguageService;
use Clavel\Basic\Models\PageRoles;
use Illuminate\Support\Facades\DB;
use Clavel\Basic\Models\PageProvider;
use Clavel\Basic\Models\PageTranslation;
use Illuminate\Support\Facades\Response;
use Yajra\DataTables\Facades\DataTables;

use App\Http\Controllers\AdminController;
use Clavel\Basic\Requests\AdminPagesRequest;
use Clavel\Basic\Models\PageProviderTranslation;

class AdminPagesController extends AdminController
{
    protected $page_title_icon = '<i class="fa fa-file-o" aria-hidden="true"></i>';

    public function __construct()
    {
        parent::__construct();

        $this->access_permission = 'admin-pages';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Si no tiene permisos para ver el listado lo echa.
        if (!auth()->user()->can('admin-pages-list')) {
            app()->abort(403);
        }

        $page_title = trans("basic::pages/admin_lang.pages");

        return view("basic::pages.admin_index", compact('page_title'))
            ->with('page_title_icon', $this->page_title_icon);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Si no tiene permisos para ver el listado lo echa.
        if (!auth()->user()->can('admin-pages-create')) {
            app()->abort(403);
        }

        $page = new Page();

        $page_title = trans("basic::pages/admin_lang.nuevo_pages");
        $page_description = '<small class="label alert-warning">' .
            trans('basic::pages/admin_lang.borrador') . '</small>';
        $form_data = array(
            'route' => array('pages.store'),
            'method' => 'POST',
            'id' => 'formData',
            'class' => 'form-horizontal'
        );
        $roles = PageRoles::all();

        // Idioma
        $serviceTranslation = new LanguageService(app()->getLocale());
        $a_trans = $serviceTranslation->getTranslations($page);

        return view('basic::pages.admin_edit', compact(
            'page_title',
            'page',
            'a_trans',
            'form_data',
            'page_description',
            'roles'
        ))
            ->with('page_title_icon', $this->page_title_icon);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AdminPagesRequest $request)
    {
        $page = new Page();
        $page->created_id = auth()->user()->id;
        $this->savePage($request, $page);

        return redirect()->to('admin/pages/' . $page->id . "/edit")
            ->with('success', trans('basic::pages/admin_lang.save_ok'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // Si no tiene permisos para modificar o visualizar lo echamos
        if (!auth()->user()->can('admin-pages-update')) {
            app()->abort(403);
        }

        $page = Page::find($id);

        $page_title = trans("basic::pages/admin_lang.modify_page");
        $page_description = ($page->active == '1') ?
            '<small class="label alert-success">' .
            trans('basic::pages/admin_lang.publicado') .
            '</small>' :
            '<small class="label alert-warning">' .
            trans('basic::pages/admin_lang.borrador') .
            '</small>';
        $form_data = array(
            'route' => array('pages.update', $page->id),
            'method' => 'PATCH',
            'id' => 'formData',
            'class' => 'form-horizontal'
        );
        $roles = PageRoles::all();

        //Meta Providers
        $a_metas_providers = $page->getArrayPageProviders();

        // Idioma
        $serviceTranslation = new LanguageService(app()->getLocale());
        $a_trans = $serviceTranslation->getTranslations($page);

        return view('basic::pages.admin_edit', compact(
            'page_title',
            'page',
            'a_trans',
            'form_data',
            'a_metas_providers',
            'page_description',
            'roles'
        ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AdminPagesRequest $request, $id)
    {
        $page = Page::find($id);
        if (empty($page)) {
            app()->abort(404);
        }
        $page->modified_id = auth()->user()->id;
        $this->savePage($request, $page);

        return redirect()->to('admin/pages/' . $page->id . "/edit")
            ->with('success', trans('basic::pages/admin_lang.save_ok'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Si no tiene permisos para modificar lo echamos
        if (!auth()->user()->can('admin-pages-delete')) {
            app()->abort(404);
        }

        $page = Page::find($id);

        if (is_null($page)) {
            app()->abort(404);
        }

        $PagesPermission = Permission::where("name", "=", $page->permission_name)->first();

        if ($PagesPermission != null) {
            $PagesPermission->delete();
        }
        $page->delete();

        return Response::json(array(
            'success' => true,
            'msg' => 'Página eliminada',
            'id' => $page->id
        ));
    }

    public function getData()
    {
        $locale = app()->getLocale();

        $pages = Page::select(
            array(
                'pages.id',
                'pages.active',
                'pt.title',
                'pt.url_seo',
                'up.first_name as c_user',
                'up2.first_name as m_user'
            )
        )
            ->join('page_translations as pt', function ($join) use ($locale) {
                $join->on('pt.page_id', '=', 'pages.id');
                $join->on('pt.locale', '=', DB::raw("'" . $locale . "'"));
            })
            ->leftJoin('user_profiles as up', 'up.user_id', '=', 'pages.created_id')
            ->leftJoin('user_profiles as up2', 'up2.user_id', '=', 'pages.modified_id');

        return Datatables::of($pages)
            ->editColumn(
                'active',
                '@if(auth()->user()->can("admin-pages-update"))
                        @if($active)
                            <button class="btn btn-success btn-sm"
                            onclick="javascript:changeStatus(\'{{ url(\'admin/pages/state/\'.$id.\'\') }}\');"
                            data-content="' . trans('general/admin_lang.descativa') . '"
                            data-placement="right" data-toggle="popover">
                                <i class="fa fa-eye" aria-hidden="true"></i>
                            </button>
                        @else
                            <button class="btn btn-danger btn-sm"
                            onclick="javascript:changeStatus(\'{{ url(\'admin/pages/state/\'.$id.\'\') }}\');"
                            data-content="' . trans('general/admin_lang.activa') . '"
                            data-placement="right" data-toggle="popover">
                                <i class="fa fa-eye-slash" aria-hidden="true"></i>
                            </button>
                        @endif
                    @else
                        @if($active)
                            <button class="btn btn-success btn-sm disabled" data-placement="right">
                                <i class="fa fa-eye" aria-hidden="true"></i>
                            </button>
                        @else
                            <button class="btn btn-danger btn-sm disabled" data-placement="right">
                                <i class="fa fa-eye" aria-hidden="true"></i>
                            </button>
                        @endif
                    @endif'
            )
            ->editColumn('url_seo', function ($row) {
                return "<a href='/pages/" . $row->url_seo . "' target='_blank'>/pages/" .
                    $row->url_seo . "</a>";
            })
            ->addColumn('actions', '
                <button class="btn bg-purple btn-sm"
                onclick="javascript:showPreview(\'{{ url(\'admin/pages/preview/\'.$id.\'\') }}\');"
                data-content="' . trans('general/admin_lang.ver') . '"
                data-placement="right" data-toggle="popover"><i class="fa fa-search" aria-hidden="true"></i></button>
                @if(auth()->user()->can("admin-pages-update"))
                    <button class="btn btn-primary btn-sm"
                    onclick="javascript:window.location=\'{{ url(\'admin/pages/\'.$id.\'/edit\') }}\';"
                    data-content="' . trans('general/admin_lang.modificar') . '"
                    data-placement="right" data-toggle="popover">
                    <i class="fa fa-pencil" aria-hidden="true"></i></button>
                @endif
                @if(auth()->user()->can("admin-pages-delete"))
                    <button class="btn btn-danger btn-sm"
                    onclick="javascript:deleteElement(\'{{ url(\'admin/pages/\'.$id.\'\') }}\');"
                    data-content="' . trans('general/admin_lang.borrar') . '"
                    data-placement="left" data-toggle="popover"><i class="fa fa-trash" aria-hidden="true"></i></button>
                @endif
                ')
            ->removeColumn('id')
            ->removeColumn('body')
            ->removeColumn('meta_title')
            ->removeColumn('meta_content')
            ->rawColumns(['active', 'actions', 'url_seo'])
            ->make();
    }

    public function setChangeState($id)
    {
        // Si no tiene permisos para modificar lo echamos
        if (!auth()->user()->can('admin-pages-update')) {
            app()->abort(404);
        }

        $page = Page::find($id);

        if (!is_null($page)) {
            $page->active = !$page->active;
            return $page->save() ? 1 : 0;
        }

        return 0;
    }

    private function savePage(Request $request, Page $page)
    {
        $page->active = $request->input("active");
        $page->css = $request->input("css");
        $page->permission = $request->input("permission");
        if (is_null($page->permission_name)) {
            $page->permission_name = "front-pages-" .
                Str::slug($request->input('userlang')[app()->getLocale()]["title"]);
        }
        $page->javascript = $request->input("javascript");
        $page->save();

        foreach ($request->input('userlang') as $key => $value) {
            $itemTrans = PageTranslation::findOrNew($value["id"]);

            $itemTrans->page_id = $page->id;
            $itemTrans->locale = $key;
            $itemTrans->title = $value["title"];
            $itemTrans->body = $value["body"];
            $itemTrans->meta_title = $value["meta_title"];
            $itemTrans->meta_content = $value["meta_content"];
            $itemTrans->save();
        }

        if (!empty($request->input('provider'))) {
            foreach ($request->input('provider') as $key => $value) {
                $pageProvider = PageProvider::firstOrNew(array("page_id" => $page->id, "provider" => $key));

                $pageProvider->page_id = $page->id;
                $pageProvider->provider = $key;
                $pageProvider->save();

                foreach ($value as $keyLang => $valueLang) {
                    foreach ($valueLang as $keyfield => $valueFields) {
                        $pageProviderTrans = PageProviderTranslation::firstOrNew(array(
                            "page_provider_id" => $pageProvider->id,
                            "locale" => $keyLang, "name" => $keyfield
                        ));
                        $pageProviderTrans->page_provider_id = $pageProvider->id;
                        $pageProviderTrans->locale = $keyLang;
                        $pageProviderTrans->name = $keyfield;
                        $pageProviderTrans->value = $valueFields;
                        $pageProviderTrans->save();
                    }
                }
            }
        }

        $this->savePermissions($page->id, $request->input("sel_roles"));
    }

    private function savePermissions($id, $roles)
    {
        $page = Page::find($id);
        if (!isset($roles) || is_null($roles) || $page->permission == '0') {
            $roles = [];
        }
        $page->roles()->sync($roles);

        $pageRole = Permission::where("name", "=", $page->permission_name)->first();
        $PagesPermission = Permission::where("name", "=", "front-pages")->first();
        $childPages = PermissionsTree::where("permissions_id", "=", $PagesPermission->id)->first();

        if (!empty($pageRole) && !is_null($pageRole)) {
            $pageRole->delete();
        }

        if ($page->permission == '1') {
            $pageRole = new Permission();
            $pageRole->display_name = 'Páginas - ' . $page->{"title:" . config('app.default_locale')};
            $pageRole->name = $page->permission_name;
            $pageRole->description = "Permiso para la página " . $page->{"title:" . config('app.default_locale')};
            $pageRole->save();

            $childPages->children()->create(['permissions_id' => $pageRole->id]);
            $a_permission_admin[] = $pageRole->id;

            foreach ($roles as $key => $value) {
                $roleAdmin = Role::find($value);
                $roleAdmin->attachPermissions($a_permission_admin);
            }
        }
    }

    public function postPagePreview(Request $request)
    {
        $page_title = $request->input("title", "");
        $css = $request->input("css");
        $javascript = $request->input("javascript");
        $body = $request->input("body");

        return view("basic::pages.admin_preview", compact('page_title', 'css', 'javascript', 'body'));
    }

    public function getPagePreview($id)
    {
        $page = Page::find($id);
        $page_title = $page->title;
        $css = $page->css;
        $javascript = $page->javascript;
        $body = $page->body;

        return view("basic::pages.admin_preview", compact('page_title', 'css', 'javascript', 'body'));
    }
}
