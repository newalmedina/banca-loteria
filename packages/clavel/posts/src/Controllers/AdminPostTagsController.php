<?php

namespace Clavel\Posts\Controllers;

use App\Http\Controllers\AdminController;
use Clavel\Posts\Requests\AdminPostTagRequest;
use Clavel\Posts\Models\PostTag;
use Clavel\Posts\Models\PostTagTranslation;
use App\Services\LanguageService;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Response;
use Yajra\DataTables\Facades\DataTables;

class AdminPostTagsController extends AdminController
{
    protected $page_title_icon = '<i class="fa fa-tags" aria-hidden="true"></i>';

    public function __construct()
    {
        parent::__construct();

        $this->access_permission = 'admin-posts-tags';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        if (!auth()->user()->can('admin-posts-tags-list')) {
            app()->abort(403);
        }

        $page_title = trans("posts::admin_lang.tags");

        return view('posts::admin_tags_index', compact('page_title'));
        // ->with('page_title_icon', $this->page_title_icon);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('admin-posts-tags-create')) {
            app()->abort(403);
        }

        $tag = new PostTag();
        $form_data = array(
            'route' => array('tags.store'),
            'method' => 'POST',
            'id' => 'formData',
            'class' => 'form-horizontal'
        );
        $page_title = trans("posts::admin_lang.nuevo_tag");

        // Idioma
        $serviceTranslation = new LanguageService(app()->getLocale());
        $a_trans = $serviceTranslation->getTranslations($tag);

        return view('posts::admin_tags_edit', compact('page_title', 'tag', 'a_trans', 'form_data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AdminPostTagRequest $request)
    {
        if (!auth()->user()->can('admin-posts-tags-create')) {
            app()->abort(404);
        }

        $tag = new PostTag();
        $this->savePostTag($request, $tag);

        return redirect()->to('admin/posts/tags/'.$tag->id."/edit")
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
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!auth()->user()->can('admin-posts-tags-update')) {
            app()->abort(403);
        }

        $tag = PostTag::find($id);

        $form_data = array(
            'route' => array('tags.update', $tag->id),
            'method' => 'PATCH',
            'id' => 'formData',
            'class' => 'form-horizontal'
        );
        $page_title = trans("posts::admin_lang.modify_tag");

        // Idioma
        $serviceTranslation = new LanguageService(app()->getLocale());
        $a_trans = $serviceTranslation->getTranslations($tag);

        return view('posts::admin_tags_edit', compact('page_title', 'tag', 'a_trans', 'form_data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AdminPostTagRequest $request, $id)
    {
        if (!auth()->user()->can('admin-posts-tags-update')) {
            app()->abort(403);
        }

        $tag = PostTag::find($id);
        if (empty($tag)) {
            app()->abort(404);
        }
        $this->savePostTag($request, $tag);

        return redirect()->to('admin/posts/tags/'.$tag->id."/edit")
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
        if (!auth()->user()->can('admin-posts-tags-delete')) {
            app()->abort(404);
        }

        $tag = PostTag::find($id);

        if (empty($tag)) {
            app()->abort(500);
        }

        $tag->delete();

        return Response::json(array(
            'success' => true,
            'msg' => 'Categoría eliminada',
            'id' => $tag->id
        ));
    }

    public function getData()
    {
        $locale = app()->getLocale();

        $tags = DB::table('post_tags as p')
        ->join('post_tag_translations as pt', function ($join) use ($locale) {
            $join->on('pt.post_tag_id', '=', 'p.id');
            $join->on('pt.locale', '=', DB::raw("'".$locale."'"));
        })
        ->select(
            array(
                'p.id',
                'p.active',
                'pt.tag'
            )
        );

        return Datatables::of($tags)
            ->editColumn(
                'active',
                '@if(auth()->user()->can("admin-posts-tags-update"))
                    @if($active)
                        <button class="btn btn-success btn-sm"
                        onclick="javascript:changeStatus(\'{{ url(\'admin/posts/tags/state/\'.$id.\'\') }}\');"
                        data-content="'.trans('general/admin_lang.descativa').'"
                        data-placement="right" data-toggle="popover">
                            <i class="fa fa-eye"></i>
                        </button>
                    @else
                        <button class="btn btn-danger btn-sm"
                        onclick="javascript:changeStatus(\'{{ url(\'admin/posts/tags/state/\'.$id.\'\') }}\');"
                        data-content="'.trans('general/admin_lang.activa').'"
                        data-placement="right" data-toggle="popover">
                            <i class="fa fa-eye-slash"></i>
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
                @endif'
            )
            ->addColumn('actions', '
                @if(auth()->user()->can("admin-posts-tags-update"))
                    <button class="btn btn-primary btn-sm"
                    onclick="javascript:window.location=\'{{ url(\'admin/posts/tags/\'.$id.\'/edit\') }}\';"
                    data-content="'.trans('general/admin_lang.modificar').'"
                    data-placement="right" data-toggle="popover">
                    <i class="fa fa-pencil" aria-hidden="true"></i>
                    </button>
                @endif
                @if(auth()->user()->can("admin-posts-tags-delete"))
                    <button class="btn btn-danger btn-sm"
                    onclick="javascript:deleteElement(\'{{ url(\'admin/posts/tags/\'.$id.\'\') }}\');"
                    data-content="'.trans('general/admin_lang.borrar').'"
                    data-placement="left" data-toggle="popover">
                    <i class="fa fa-trash" aria-hidden="true"></i></button>
                @endif
                ')
            ->removeColumn('id')
            ->rawColumns(['active', 'actions'])
            ->make();
    }

    public function setChangeState($id)
    {
        // Si no tiene permisos para modificar lo echamos
        if (!auth()->user()->can('admin-posts-tags-update')) {
            app()->abort(403);
        }

        $tag = PostTag::find($id);

        if (!empty($tag)) {
            $tag -> active = !$tag -> active;
            return $tag -> save() ? 1 : 0 ;
        }

        return 0;
    }

    private function savePostTag(AdminPostTagRequest $request, PostTag $postTag)
    {
        $postTag->active = $request->input("active");
        $postTag->save();

        foreach ($request->input('userlang') as $key => $value) {
            $itemTrans = PostTagTranslation::findOrNew($value["id"]);

            $itemTrans->post_tag_id = $postTag->id;
            $itemTrans->locale = $key;
            $itemTrans->tag = $value["tag"];
            $itemTrans->save();
        }
    }
}
