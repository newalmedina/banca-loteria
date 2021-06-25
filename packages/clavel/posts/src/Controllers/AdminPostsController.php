<?php

namespace Clavel\Posts\Controllers;

use App\Http\Controllers\AdminController;
use App\Models\User;
use Clavel\Posts\Jobs\NotifyPostJob;
use Clavel\Posts\Requests\AdminPostRequest;
use App\Models\Permission;
use App\Models\PermissionsTree;
use Clavel\Posts\Models\Post;
use Clavel\Posts\Models\PostImage;
use Clavel\Posts\Models\PostRoles;
use Clavel\Posts\Models\PostTag;
use Clavel\Posts\Models\PostTranslation;
use App\Models\Role;
use App\Services\LanguageService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Response;
use Yajra\DataTables\Facades\DataTables;
use Notification;

class AdminPostsController extends AdminController
{
    protected $page_title_icon = '<i class="fa fa-newspaper-o" aria-hidden="true"></i>';

    public function __construct()
    {
        parent::__construct();

        $this->access_permission = 'admin-posts';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Si no tiene permisos para ver el listado lo echa.
        if (!auth()->user()->can('admin-posts-list')) {
            app()->abort(403);
        }

        $page_title = trans("posts::admin_lang.news");

        return view("posts::admin_index", compact('page_title'))
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
        if (!auth()->user()->can('admin-posts-create')) {
            app()->abort(403);
        }

        $post = new Post();
        $form_data = array(
            'route' => array('posts.store'),
            'method' => 'POST',
            'id' => 'formData',
            'class' => 'form-horizontal'
        );
        $page_title = trans("posts::admin_lang.nuevo_newpost");
        $page_description = '<small class="label alert-warning">'.trans('posts::admin_lang.borrador').'</small>';
        $roles = PostRoles::all();

        // Idioma
        $serviceTranslation = new LanguageService(app()->getLocale());
        $a_trans = $serviceTranslation->getTranslations($post);

        $tags = PostTag::actives()->get();

        // Autores
        $autores = User::select(
            ['users.id', DB::Raw('CONCAT(user_profiles.first_name, " ", user_profiles.last_name) as fullName')]
        )
            ->join('user_profiles', function ($join) {
                $join->on('user_profiles.user_id', '=', 'users.id');
            })
            ->active()
            ->get()
            ->pluck('fullName', 'id');

        $autores->prepend(trans("posts::admin_lang.no_author"), '');

        return view(
            'posts::admin_edit',
            compact(
                'page_title',
                'post',
                'a_trans',
                'form_data',
                'page_description',
                'roles',
                'tags',
                'autores'
            )
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AdminPostRequest $request)
    {
        if (!auth()->user()->can('admin-posts-create')) {
            app()->abort(403);
        }

        $post = new Post();
        $this->savePost($request, $post);

        return redirect()->to('admin/posts/'.$post->id."/edit")
            ->with('success', trans('posts::admin_lang.save_ok'));
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
        if (!auth()->user()->can('admin-posts-update')) {
            app()->abort(403);
        }

        $post = Post::find($id);

        $form_data = array(
            'route' => array('posts.update', $post->id),
            'method' => 'PATCH',
            'id' => 'formData',
            'class' => 'form-horizontal'
        );
        $page_title = trans("posts::admin_lang.modify_post");
        $page_description = ($post->active=='1') ?
            '<small class="label alert-success">'.trans('posts::admin_lang.publicado').'</small>' :
            '<small class="label alert-warning">'.trans('posts::admin_lang.borrador').'</small>';

        // Idioma
        $serviceTranslation = new LanguageService(app()->getLocale());
        $a_trans = $serviceTranslation->getTranslations($post);

        $roles = PostRoles::all();
        $tags = PostTag::actives()->get();

        // Autores
        $autores = User::select(
            ['users.id', DB::Raw('CONCAT(user_profiles.first_name, " ", user_profiles.last_name) as fullName')]
        )
            ->join('user_profiles', function ($join) {
                $join->on('user_profiles.user_id', '=', 'users.id');
            })
            ->active()
            ->get()
            ->pluck('fullName', 'id');

        $autores->prepend(trans("posts::admin_lang.no_author"), '');


        return view(
            'posts::admin_edit',
            compact(
                'page_title',
                'post',
                'a_trans',
                'form_data',
                'page_description',
                'roles',
                'tags',
                'autores'
            )
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AdminPostRequest $request, $id)
    {
        if (!auth()->user()->can('admin-posts-update')) {
            app()->abort(403);
        }

        $post = Post::find($id);
        if (empty($post)) {
            app()->abort(500);
        }
        $this->savePost($request, $post);

        return redirect()->to('admin/posts/'.$post->id."/edit")
            ->with('success', trans('posts::admin_lang.save_ok'));
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
        if (!auth()->user()->can('admin-posts-delete')) {
            app()->abort(403);
        }

        $post = Post::find($id);

        if (empty($post)) {
            app()->abort(500);
        }

        $PagesPermission = Permission::where("name", "=", $post->permission_name)->first();

        if ($PagesPermission!=null) {
            $PagesPermission->delete();
        }
        $post->delete();

        return Response::json(array(
            'success' => true,
            'msg' => 'Post eliminada',
            'id' => $post->id
        ));
    }

    public function getData()
    {
        $locale = app()->getLocale();

        $news = DB::table('posts as p')
        ->join('post_translations as pt', function ($join) use ($locale) {
            $join->on('pt.post_id', '=', 'p.id');
            $join->on('pt.locale', '=', DB::raw("'".$locale."'"));
        })
        ->select(
            array(
                'p.id',
                'p.active',
                'pt.title',
                'pt.url_seo',
                'p.date_post',
                'p.in_home'
            )
        );

        return Datatables::of($news)
            ->editColumn(
                'active',
                '@if(auth()->user()->can("admin-posts-update"))
                    @if($active)
                        <button class="btn btn-success btn-sm"
                        onclick="javascript:changeStatus(\'{{ url(\'admin/posts/state/\'.$id.\'\') }}\');"
                        data-content="'.trans('general/admin_lang.descativa').'"
                        data-placement="right" data-toggle="popover">
                            <i class="fa fa-eye"></i>
                        </button>
                    @else
                        <button class="btn btn-danger btn-sm"
                        onclick="javascript:changeStatus(\'{{ url(\'admin/posts/state/\'.$id.\'\') }}\');"
                        data-content="'.trans('general/admin_lang.activa').'"
                        data-placement="right" data-toggle="popover">
                            <i class="fa fa-eye-slash"></i>
                        </button>
                    @endif
                    @if($in_home)
                        <button class="btn btn-success btn-sm"
                        onclick="javascript:changeStatus(\'{{ url(\'admin/posts/state_home/\'.$id.\'\') }}\');"
                        data-content="'.trans('posts::admin_lang.descativa_home').'"
                        data-placement="right" data-toggle="popover">
                            <i class="fa fa-home" aria-hidden="true"></i>
                        </button>
                    @else
                        <button class="btn btn-danger btn-sm"
                        onclick="javascript:changeStatus(\'{{ url(\'admin/posts/state_home/\'.$id.\'\') }}\');"
                        data-content="'.trans('posts::admin_lang.activa_home').'"
                        data-placement="right" data-toggle="popover">
                            <i class="fa fa-home" aria-hidden="true"></i>
                        </button>
                    @endif
                @else
                    @if($active)
                        <button class="btn btn-success btn-sm disabled" data-placement="right">
                            <i class="fa fa-eye"></i>
                        </button>
                    @else
                        <button class="btn btn-danger btn-sm disabled" data-placement="right">
                            <i class="fa fa-eye"></i>
                        </button>
                    @endif
                    @if($in_home)
                        <button class="btn btn-success btn-sm disabled" data-placement="right">
                            <i class="fa fa-home" aria-hidden="true"></i>
                        </button>
                    @else
                        <button class="btn btn-danger btn-sm disabled" data-placement="right">
                            <i class="fa fa-home" aria-hidden="true"></i>
                        </button>
                    @endif
                @endif'
            )
            ->editColumn('url_seo', function ($row) {
                return "<a href='/posts/post/".$row->url_seo."'
                target='_blank'>/posts/post/".$row->url_seo."</a>";
            })
            ->editColumn('date_post', function ($row) {
                $fecha = new Carbon($row->date_post);
                return $fecha->format('d/m/Y');
            })
            ->addColumn('actions', '
                @if(auth()->user()->can("admin-posts-update"))
                    <button class="btn btn-primary btn-sm"
                    onclick="javascript:window.location=\'{{ url(\'admin/posts/\'.$id.\'/edit\') }}\';"
                    data-content="'.trans('general/admin_lang.modificar').'"
                    data-placement="right" data-toggle="popover">
                    <i class="fa fa-pencil" aria-hidden="true"></i>
                    </button>
                @endif
                @if(auth()->user()->can("admin-posts-delete"))
                    <button class="btn btn-danger btn-sm"
                    onclick="javascript:deleteElement(\'{{ url(\'admin/posts/\'.$id.\'\') }}\');"
                    data-content="'.trans('general/admin_lang.borrar').'"
                    data-placement="left" data-toggle="popover"><i class="fa fa-trash" aria-hidden="true"></i></button>
                @endif
                @if(auth()->user()->can("admin-posts-notiffy") && $active)
                    <button class="btn bg-purple btn-sm"
                    onclick="javascript:sendNotify(\'{{ url(\'admin/posts/notify/\'.$id.\'\') }}\');"
                    data-content="'.trans('posts::admin_lang.notify_users').'"
                    data-placement="right" data-toggle="popover">
                        <i class="fa fa-envelope-o"></i>
                    </button>
                @endif
                ')
            ->removeColumn('id')
            ->removeColumn('in_home')
            ->removeColumn('body')
            ->removeColumn('meta_title')
            ->removeColumn('meta_content')
            ->rawColumns(['active', 'actions', 'url_seo'])
            ->make();
    }

    public function setChangeState($id)
    {
        // Si no tiene permisos para modificar lo echamos
        if (!auth()->user()->can('admin-posts-update')) {
            app()->abort(403);
        }

        $post = Post::find($id);

        if (!empty($post)) {
            $post -> active = !$post -> active;
            return $post -> save() ? 1 : 0 ;
        }

        return 0;
    }

    public function setChangeHome($id)
    {
        // Si no tiene permisos para modificar lo echamos
        if (!auth()->user()->can('admin-posts-update')) {
            app()->abort(403);
        }

        $post = Post::find($id);

        if (!is_null($post)) {
            $post -> in_home = !$post -> in_home;
            return $post -> save() ? 1 : 0 ;
        }

        return 0;
    }

    private function savePost(Request $request, Post $post)
    {
        $post->date_post = ($request->input("date_post")!='') ?
            Carbon::createFromFormat('d/m/Y', $request->input("date_post")) : null;
        $post->date_activation = ($request->input("date_activation")!='') ?
            Carbon::createFromFormat('d/m/Y', $request->input("date_activation")) : null;
        $post->date_deactivation = ($request->input("date_deactivation")!='') ?
            Carbon::createFromFormat('d/m/Y', $request->input("date_deactivation")) : null;
        $post->in_home = $request->input("in_home");
        $post->date_deactivation_home = ($request->input("date_deactivation_home")!='') ?
            Carbon::createFromFormat('d/m/Y', $request->input("date_deactivation_home")) : null;
        $post->active = $request->input("active");
        $post->has_shared = $request->input("has_shared");
        $post->has_comment = $request->input("has_comment");
        $post->has_comment_only_user = $request->input("has_comment_only_user");
        $post->permission = $request->input("permission");
        if (is_null($post->permission_name)) {
            $post->permission_name = "front-posts-".str_slug($request->input('userlang')[app()->getLocale()]["title"]);
        }

        $post->author_id = $request->input("author_id", null);

        $post->save();

        foreach ($request->input('userlang') as $key => $value) {
            $itemTrans = PostTranslation::findOrNew($value["id"]);

            $itemTrans->post_id = $post->id;
            $itemTrans->locale = $key;
            $itemTrans->title = $value["title"];
            $itemTrans->body = $value["body"];
            $itemTrans->meta_title = $value["meta_title"];
            $itemTrans->meta_content = $value["meta_content"];
            $itemTrans->save();
        }

        $post->images()->delete();

        if ($request->input('image') && !is_null($request->input('image'))) {
            foreach ($request->input('image') as $value) {
                if ($value!='') {
                    $itemImage = new PostImage();

                    $itemImage->post_id = $post->id;
                    $itemImage->path = $value;
                    $itemImage->save();
                }
            }
        }

        $tag_ids = $request->input('sel_tags');
        $post->tags()->detach();
        if (!is_null($request->input('sel_tags'))) {
            $post->tags()->sync($tag_ids);
        }

        $this->savePermissions($post->id, $request->input("sel_roles"));
    }

    private function savePermissions($id, $roles)
    {
        // Si no hay roles o los permisos son para todos o todos los usuarios registrados, los roles no tienen sentido
        $post = Post::find($id);
        if (!isset($roles) || is_null($roles) || $post->permission=='0' || $post->permission=='2') {
            $roles = [];
        }
        $post->roles()->sync($roles);

        $pageRole = Permission::where("name", "=", $post->permission_name)->first();
        $PagesPermission = Permission::where("name", "=", "front-posts")->first();
        $childPages = PermissionsTree::where("permissions_id", "=", $PagesPermission->id)->first();

        if (!empty($pageRole) && !is_null($pageRole)) {
            $pageRole->delete();
        }

        if ($post->permission=='1') {
            $pageRole = new Permission();
            $pageRole->display_name = 'posts - '.$post->{"title:".app()->getLocale()};
            $pageRole->name = $post->permission_name;
            $pageRole->description = "Permiso para la página ".$post->{"title:".app()->getLocale()};
            $pageRole->save();

            $childPages->children()->create(['permissions_id' => $pageRole->id]);
            $this->a_permission_admin[] = $pageRole->id;

            foreach ($roles as $key => $value) {
                $roleAdmin = Role::find($value);
                $roleAdmin->attachPermissions($this->a_permission_admin);
            }
        }
    }

    public function notifyPost($id)
    {
        // Si no tiene permisos para modificar lo echamos
        if (!auth()->user()->can('admin-posts-update')) {
            app()->abort(403);
        }

        $post = Post::find($id);

        if (empty($post)) {
            return;
        }

        try {
            // Creamos un Job para enviar el email a todos los usuarios en segundo plano y así no bloquear la UI
            NotifyPostJob::dispatch($post);

            return Response::json(array(
                'success' => true,
                'msg' => trans('posts::admin_lang.notify_sended'),
                'id' => $post->id
            ));
        } catch (\Exception $e) {
            return;
        }
    }
}
