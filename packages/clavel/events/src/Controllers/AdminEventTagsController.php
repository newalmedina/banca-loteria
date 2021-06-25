<?php

namespace App\Modules\Events\Controllers;

use App\Http\Controllers\AdminController;
use App\Modules\Events\Requests\AdminEventTagRequest;
use App\Modules\Events\Models\EventTag;
use App\Modules\Events\Models\EventTagTranslation;
use App\Services\LanguageService;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Response;
use Yajra\DataTables\Facades\DataTables;

class AdminEventTagsController extends AdminController
{
    protected $page_title_icon = '<i class="fa fa-tags" aria-hidden="true"></i>';

    public function __construct()
    {
        parent::__construct();

        $this->access_permission = 'admin-events-tags';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Si no tiene permisos para ver el listado lo echa.
        if (!auth()->user()->can('admin-events-tags-list')) {
            app()->abort(403);
        }

        $page_title = trans("Events::admin_lang.tags");

        return view('Events::admin_tags_index', compact('page_title'));
        //->with('page_title_icon', $this->page_title_icon);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('admin-events-tags-create')) {
            app()->abort(403);
        }

        $tag = new EventTag();
        $form_data = array(
            'route' => array('tags.store'),
            'method' => 'POST',
            'id' => 'formData',
            'class' => 'form-horizontal'
        );
        $page_title = trans("Events::admin_lang.nuevo_tag");

        // Idioma
        $serviceTranslation = new LanguageService(app()->getLocale());
        $a_trans = $serviceTranslation->getTranslations($tag);

        return view(
            'Events::admin_tags_edit',
            compact(
                'page_title',
                'tag',
                'a_trans',
                'form_data'
            )
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AdminEventTagRequest $request)
    {
        if (!auth()->user()->can('admin-events-tags-create')) {
            app()->abort(404);
        }

        $tag = new EventTag();
        $this->saveEventTag($request, $tag);

        return redirect()->to('admin/events/tags/'.$tag->id."/edit")
            ->with('success', trans('Events::admin_lang.save_ok'));
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
        if (!auth()->user()->can('admin-events-tags-update')) {
            app()->abort(403);
        }

        $tag = EventTag::find($id);

        $form_data = array(
            'route' => array('tags.update', $tag->id),
            'method' => 'PATCH',
            'id' => 'formData',
            'class' => 'form-horizontal'
        );
        $page_title = trans("Events::admin_lang.modify_tag");

        // Idioma
        $serviceTranslation = new LanguageService(app()->getLocale());
        $a_trans = $serviceTranslation->getTranslations($tag);

        return view('Events::admin_tags_edit', compact('page_title', 'tag', 'a_trans', 'form_data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AdminEventTagRequest $request, $id)
    {
        if (!auth()->user()->can('admin-events-tags-update')) {
            app()->abort(403);
        }

        $tag = EventTag::find($id);
        if (empty($tag)) {
            app()->abort(404);
        }
        $this->saveEventTag($request, $tag);

        return redirect()->to('admin/events/tags/'.$tag->id."/edit")
            ->with('success', trans('Events::admin_lang.save_ok'));
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
        if (!auth()->user()->can('admin-events-tags-delete')) {
            app()->abort(404);
        }

        $tag = EventTag::find($id);

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

        $tags = DB::table('event_tags as p')
            ->join('event_tag_translations as pt', function ($join) use ($locale) {
                $join->on('pt.event_tag_id', '=', 'p.id');
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
                '@if(auth()->user()->can("admin-events-tags-update"))
                        @if($active)
                            <button class="btn btn-success btn-sm"
                            onclick="javascript:changeStatus(\'{{ url(\'admin/events/tags/state/\'.$id.\'\') }}\');"
                            data-content="'.trans('general/admin_lang.descativa').'"
                            data-placement="right" data-toggle="popover">
                                <i class="fa fa-eye" aria-hidden="true"></i>
                            </button>
                        @else
                            <button class="btn btn-danger btn-sm"
                            onclick="javascript:changeStatus(\'{{ url(\'admin/events/tags/state/\'.$id.\'\') }}\');"
                            data-content="'.trans('general/admin_lang.activa').'"
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
            ->addColumn('actions', '
                @if(auth()->user()->can("admin-events-tags-update"))
                    <button class="btn btn-primary btn-sm"
                    onclick="javascript:window.location=\'{{ url(\'admin/events/tags/\'.$id.\'/edit\') }}\';"
                    data-content="'.trans('general/admin_lang.modificar').'"
                    data-placement="right" data-toggle="popover">
                    <i class="fa fa-pencil" aria-hidden="true"></i></button>
                @endif
                @if(auth()->user()->can("admin-events-tags-delete"))
                    <button class="btn btn-danger btn-sm"
                    onclick="javascript:deleteElement(\'{{ url(\'admin/events/tags/\'.$id.\'\') }}\');"
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
        if (!auth()->user()->can('admin-events-tags-update')) {
            app()->abort(403);
        }

        $tag = EventTag::find($id);

        if (!empty($tag)) {
            $tag -> active = !$tag -> active;
            return $tag -> save() ? 1 : 0 ;
        }

        return 0;
    }

    private function saveEventTag(AdminEventTagRequest $request, EventTag $eventTag)
    {
        $eventTag->active = $request->input("active");
        $eventTag->save();

        foreach ($request->input('userlang') as $key => $value) {
            $itemTrans = EventTagTranslation::findOrNew($value["id"]);

            $itemTrans->event_tag_id = $eventTag->id;
            $itemTrans->locale = $key;
            $itemTrans->tag = $value["tag"];
            $itemTrans->save();
        }
    }
}
