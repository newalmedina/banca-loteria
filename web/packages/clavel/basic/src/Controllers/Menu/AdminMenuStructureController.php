<?php

namespace Clavel\Basic\Controllers\Menu;

use App\Models\Permission;
use Illuminate\Support\Str;
use Clavel\Basic\Models\Page;
use App\Services\LanguageService;
use Clavel\Basic\Models\MenuItem;
use Clavel\Basic\Models\MenuItemRoles;
use Clavel\Basic\Models\MenuItemTypes;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\AdminController;

use Clavel\Basic\Models\MenuItemTranslation;
use Clavel\Basic\Requests\AdminMenuStructureRequest;

class AdminMenuStructureController extends AdminController
{
    protected $page_title_icon = '<i class="fa fa-share-alt"></i>';

    public function __construct()
    {
        parent::__construct();

        $this->access_permission = 'admin-menu';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($menu_id)
    {
        // Si no tiene permisos para ver el listado lo echa.
        if (!auth()->user()->can('admin-menu-update')) {
            app()->abort(403);
        }

        $page_title = trans("basic::menu/admin_lang.structure");

        $menuTree = MenuItem::where('menu_id', "=", $menu_id)->withDepth()->get()->sortBy('lft');

        return view("basic::menu.admin_structure", compact('page_title', "menuTree", "menu_id"))
            ->with('page_title_icon', $this->page_title_icon);
    }

    public function openform($menu_id, $idnode)
    {
        if (!auth()->user()->can('admin-menu-update')) {
            app()->abort(403);
        }

        $item = MenuItem::findOrNew($idnode);

        $idtypes = MenuItemTypes::all();
        $pages = Page::where("active", "=", "1")->get();

        $form_data = array(
            'route' => array('admin.menu.structure.store'),
            'method' => 'POST',
            'id' => 'formData',
            'class' => 'form-horizontal'
        );

        // Idioma
        $serviceTranslation = new LanguageService(app()->getLocale());
        $a_trans = $serviceTranslation->getTranslations($item);

        // Roles del menu
        $roles = MenuItemRoles::all();

        return view(
            'basic::menu.admin_structure_form',
            compact(
                "menu_id",
                'item',
                'form_data',
                'idtypes',
                'a_trans',
                'pages',
                'roles'
            )
        );
    }

    public function store(AdminMenuStructureRequest $request)
    {
        $item = MenuItem::findOrNew($request->input("id"));

        $item->menu_id = $request->input("menu_id");
        $item->item_type_id = $request->input("item_type_id");
        $item->page_id = ($request->input("page_id") == '') ? null : $request->input('page_id');
        $item->target = ($request->input("target") == '') ? null : $request->input('target');
        $item->module_name = $request->input("module_name", "");
        if (empty($item->module_name)) {
            $item->module_name = $request->input("system_name", "");
        }

        $item->uri = $request->input("uri");
        $item->status = $request->input("status");
        $item->permission = $request->input("permission");
        if (empty($item->permission_name)) {
            $item->permission_name = "front-menus-items-" .
                Str::slug($request->input('userlang')[app()->getLocale()]["title"]);
        }
        $item->save();

        if (empty($request->input("id"))) {
            $item->makeRoot();
        }

        foreach ($request->input('userlang') as $key => $value) {
            $itemTrans = MenuItemTranslation::findOrNew($value["id"]);

            $itemTrans->menu_item_id = $item->id;
            $itemTrans->locale = $key;
            $itemTrans->title = (!empty($value["title"]) ? $value["title"] : "");


            switch ($item->menuItemType->slug) {
                case "pagina":
                    $my_url = (!is_null($item->page_id)) ?
                        "pages/" . $item->page->{'url_seo:' . $key} :
                        "pages/blank/" . $item->id;
                    break;
                case "modulo":
                    $my_url = preg_replace("{^/}", '', $item->module_name);
                    break;
                case "interno":
                    $my_url = preg_replace("{^/}", '', $item->uri);
                    break;
                case "externo":
                    $my_url = $value["url"];
                    break;
                case "system":
                    $my_url = preg_replace("{^/}", '', $item->system_name);
                    break;
                default:
                    $my_url = "/";
                    break;
            }
            $itemTrans->generate_url = $my_url;
            $itemTrans->url = $value["url"];
            $itemTrans->save();
        }

        $this->savePermissions($item->id, $request->input("sel_roles"));

        return redirect()->to('admin/menu/structure/' . $request->input("menu_id"))
            ->with('success', trans('basic::menu/admin_lang.save_ok'));
    }

    private function savePermissions($id, $roles)
    {
        $item = MenuItem::find($id);
        if (!isset($roles) || is_null($roles) || $item->permission == '0') {
            $roles = [];
        }
        $item->roles()->sync($roles);
    }

    public function destroy($node_id)
    {
        // Si no tiene permisos para modificar lo echamos
        if (!auth()->user()->can('admin-menu-delete')) {
            app()->abort(403);
        }

        $item = MenuItem::find($node_id);

        if (is_null($item)) {
            app()->abort(404);
        }

        $permissions = Permission::where("name", "=", $item->permission_name)->first();

        if ($permissions != null) {
            $permissions->delete();
        }

        $item->delete();

        return Response::json(array(
            'success' => true,
            'msg' => 'Menu eliminado',
            'id' => $item->id
        ));
    }

    public function reordenarArbol($menu_id, $node_id, $parent_id, $previous)
    {
        $node = MenuItem::find($node_id);

        if ($previous != '0') {
            // Añadimos el elemento detras del previo
            $prev = MenuItem::find($previous);
            $node->afterNode($prev)->save();
        } else {
            if (!empty($parent_id)) {
                $parent = MenuItem::find($parent_id);
                try {
                    $node->prependToNode($parent)->save();
                } catch (\Exception  $ex) {
                    return "error";
                }
            } else {
                // Añadimos el elemento delante del indicado
                $parent = MenuItem::where("menu_id", "=", $menu_id)->orderBy('lft')->first();
                if ($parent->id !== $node->id) {
                    $node->beforeNode($parent)->save();
                }
            }
        }
    }
}
