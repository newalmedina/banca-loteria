<?php

namespace App\Modules\Events\Controllers;

use App\Http\Controllers\AdminController;
use App\Modules\Events\Requests\AdminEventRequest;
use App\Modules\Events\Models\Event;
use App\Modules\Events\Models\EventImage;
use App\Modules\Events\Models\EventRoles;
use App\Modules\Events\Models\EventTag;
use App\Modules\Events\Models\EventTranslation;
use App\Models\Permission;
use App\Models\PermissionsTree;
use App\Models\Role;
use App\Services\LanguageService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Response;
use Yajra\DataTables\Facades\DataTables;

class AdminEventsController extends AdminController
{
    protected $page_title_icon = '<i class="fa fa-calendar" hidden="true" aria-hidden="true"></i>';

    public function __construct()
    {
        parent::__construct();

        $this->access_permission = 'admin-events';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Si no tiene permisos para ver el listado lo echa.
        if (!auth()->user()->can('admin-events-list')) {
            app()->abort(403);
        }

        $page_title = trans("Events::admin_lang.events");

        return view("Events::admin_index", compact('page_title'))
            ->with('page_title_icon', $this->page_title_icon);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('admin-events-create')) {
            app()->abort(403);
        }

        $event = new Event();
        $form_data = array(
            'route' => array('events.store'),
            'method' => 'POST',
            'id' => 'formData',
            'class' => 'form-horizontal'
        );
        $page_title = trans("Events::admin_lang.nuevo_newpost");
        $page_description = '<small class="label alert-warning">' . trans('Events::admin_lang.borrador') . '</small>';
        $roles = EventRoles::all();

        // Idioma
        $serviceTranslation = new LanguageService(app()->getLocale());
        $a_trans = $serviceTranslation->getTranslations($event);

        $tags = EventTag::actives()->get();

        return view(
            'Events::admin_edit',
            compact(
                'page_title',
                'event',
                'a_trans',
                'form_data',
                'page_description',
                'roles',
                'tags'
            )
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AdminEventRequest $request)
    {
        if (!auth()->user()->can('admin-events-create')) {
            app()->abort(403);
        }

        $event = new Event();
        $event->user_id = auth()->user()->id;
        $this->saveEvent($request, $event);

        return redirect()->to('admin/events/' . $event->id . "/edit")
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
        if (!auth()->user()->can('admin-events-update')) {
            app()->abort(403);
        }

        $event = Event::find($id);

        $form_data = array(
            'route' => array('events.update', $event->id),
            'method' => 'PATCH',
            'id' => 'formData',
            'class' => 'form-horizontal'
        );
        $page_title = trans("Events::admin_lang.modify_page");
        $page_description = ($event->active == '1') ?
            '<small class="label alert-success">' . trans('Events::admin_lang.publicado') . '</small>' :
            '<small class="label alert-warning">' . trans('Events::admin_lang.borrador') . '</small>';

        // Idioma
        $serviceTranslation = new LanguageService(app()->getLocale());
        $a_trans = $serviceTranslation->getTranslations($event);

        $roles = EventRoles::all();
        $tags = EventTag::actives()->get();

        return view(
            'Events::admin_edit',
            compact(
                'page_title',
                'event',
                'a_trans',
                'form_data',
                'page_description',
                'roles',
                'tags'
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
    public function update(AdminEventRequest $request, $id)
    {
        if (!auth()->user()->can('admin-events-update')) {
            app()->abort(403);
        }

        $event = Event::find($id);


        if (empty($event)) {
            app()->abort(500);
        }

        $this->saveEvent($request, $event);

        return redirect()->to('admin/events/' . $event->id . "/edit")
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
        if (!auth()->user()->can('admin-events-delete')) {
            app()->abort(403);
        }

        $event = Event::find($id);

        if (empty($event)) {
            app()->abort(500);
        }

        $permissions = Permission::where("name", "=", $event->permission_name)->first();

        if ($permissions != null) {
            $permissions->delete();
        }
        $event->delete();

        return Response::json(array(
            'success' => true,
            'msg' => 'Evento eliminado',
            'id' => $event->id
        ));
    }

    public function getData()
    {
        $locale = app()->getLocale();

        $events =  DB::table('events as e')
            ->join('event_translations as et', function ($join) use ($locale) {
                $join->on('et.event_id', '=', 'e.id');
                $join->on('et.locale', '=', DB::raw("'" . $locale . "'"));
            })
            ->select(
                array(
                    'e.id',
                    'e.active',
                    'et.title',
                    'et.url_seo',
                    'e.date_start',
                    'e.in_home',
                )
            );

        return Datatables::of($events)
            ->editColumn(
                'active',
                '@if(auth()->user()->can("admin-events-update"))
                    @if($active)
                        <button class="btn btn-success btn-sm"
                        onclick="javascript:changeStatus(\'{{ url(\'admin/events/state/\'.$id.\'\') }}\');"
                        data-content="' . trans('general/admin_lang.descativa') . '"
                        data-placement="right"
                        data-toggle="popover">
                            <i class="fa fa-eye" aria-hidden="true"></i>
                        </button>
                    @else
                        <button class="btn btn-danger btn-sm"
                        onclick="javascript:changeStatus(\'{{ url(\'admin/events/state/\'.$id.\'\') }}\');"
                        data-content="' . trans('general/admin_lang.activa') . '"
                        data-placement="right" data-toggle="popover">
                            <i class="fa fa-eye-slash" aria-hidden="true"></i>
                        </button>
                    @endif
                    @if($in_home)
                        <button class="btn btn-success btn-sm"
                        onclick="javascript:changeStatus(\'{{ url(\'admin/events/state_home/\'.$id.\'\') }}\');"
                        data-content="' . trans('Events::admin_lang.descativa_home') . '"
                        data-placement="right" data-toggle="popover">
                            <i class="fa fa-home" aria-hidden="true"></i>
                        </button>
                    @else
                        <button class="btn btn-danger btn-sm"
                        onclick="javascript:changeStatus(\'{{ url(\'admin/events/state_home/\'.$id.\'\') }}\');"
                        data-content="' . trans('Events::admin_lang.activa_home') . '"
                        data-placement="right" data-toggle="popover">
                            <i class="fa fa-home" aria-hidden="true"></i>
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
                return "<a href='/events/detalle/" . $row->url_seo . "'
                target='_blank'>/events/detalle/" . $row->url_seo . "</a>";
            })
            ->editColumn('date_start', function ($row) {
                $fecha = new Carbon($row->date_start);
                return $fecha->format('d/m/Y');
            })
            ->addColumn('actions', '
                @if(auth()->user()->can("admin-events-update"))
                    <button class="btn btn-primary btn-sm"
                    onclick="javascript:window.location=\'{{ url(\'admin/events/\'.$id.\'/edit\') }}\';"
                    data-content="' . trans('general/admin_lang.modificar') . '"
                    data-placement="right" data-toggle="popover">
                    <i class="fa fa-pencil" aria-hidden="true"></i></button>
                @endif
                @if(auth()->user()->can("admin-events-delete"))
                    <button class="btn btn-danger btn-sm"
                    onclick="javascript:deleteElement(\'{{ url(\'admin/events/\'.$id.\'\') }}\');"
                    data-content="' . trans('general/admin_lang.borrar') . '"
                    data-placement="left" data-toggle="popover"><i class="fa fa-trash" aria-hidden="true"></i></button>
                @endif
                ')
            ->removeColumn('id')
            ->removeColumn('in_home')
            ->removeColumn('body')
            ->removeColumn('link')
            ->removeColumn('localization')
            ->rawColumns(['active', 'actions', 'url_seo'])
            ->make();
    }

    public function setChangeState($id)
    {
        // Si no tiene permisos para modificar lo echamos
        if (!auth()->user()->can('admin-events-update')) {
            app()->abort(403);
        }

        $event = Event::find($id);

        if (!empty($event)) {
            $event->active = !$event->active;
            return $event->save() ? 1 : 0;
        }

        return 0;
    }

    public function setChangeHome($id)
    {
        // Si no tiene permisos para modificar lo echamos
        if (!auth()->user()->can('admin-events-update')) {
            app()->abort(403);
        }

        $event = Event::find($id);

        if (!empty($event)) {
            $event->in_home = !$event->in_home;
            return $event->save() ? 1 : 0;
        }

        return 0;
    }

    private function saveEvent(AdminEventRequest $request, Event $event)
    {
        $event->date_start = ($request->input("date_start") != '') ?
            Carbon::createFromFormat('d/m/Y', $request->input("date_start")) : null;
        $event->date_end = ($request->input("date_end") != '') ?
            Carbon::createFromFormat('d/m/Y', $request->input("date_end")) : null;
        $event->in_home = $request->input("in_home");
        $event->active = $request->input("active");
        $event->has_shared = $request->input("has_shared");
        $event->permission = $request->input("permission");
        if (empty($event->permission_name)) {
            $event->permission_name = "front-events-" .
                str_slug($request->input('userlang')[app()->getLocale()]["title"]);
        }
        $event->save();

        foreach ($request->input('userlang') as $key => $value) {
            $itemTrans = EventTranslation::findOrNew($value["id"]);

            $itemTrans->event_id = $event->id;
            $itemTrans->locale = $key;
            $itemTrans->title = $value["title"];
            $itemTrans->body = $value["body"];
            $itemTrans->localization = $value["localization"];
            $itemTrans->link = $value["link"];
            $itemTrans->save();
        }

        $event->images()->delete();

        if ($request->input('image', [])) {
            foreach ($request->input('image') as $value) {
                if ($value != '') {
                    $itemImage = new EventImage();

                    $itemImage->event_id = $event->id;
                    $itemImage->path = $value;
                    $itemImage->save();
                }
            }
        }

        $tag_ids = $request->input('sel_tags');
        $event->tags()->detach();
        if (!empty($request->input('sel_tags'))) {
            $event->tags()->sync($tag_ids);
        }

        $this->savePermissions($event->id, $request->input("sel_roles"));
    }

    private function savePermissions($id, $roles)
    {
        $event = Event::find($id);
        if (!isset($roles) || is_null($roles) || $event->permission == '0') {
            $roles = [];
        }
        $event->roles()->sync($roles);

        $pageRole = Permission::where("name", "=", $event->permission_name)->first();
        $PagesPermission = Permission::where("name", "=", "front-events")->first();
        $childPages = PermissionsTree::where("permissions_id", "=", $PagesPermission->id)->first();

        if (!empty($pageRole) && !is_null($pageRole)) {
            $pageRole->delete();
        }

        if ($event->permission == '1') {
            $pageRole = new Permission();
            $pageRole->display_name = 'Events - ' . $event->{"title:" . app()->getLocale()};
            $pageRole->name = $event->permission_name;
            $pageRole->description = "Permiso para el event " . $event->{"title:" . app()->getLocale()};
            $pageRole->save();

            $childPages->children()->create(['permissions_id' => $pageRole->id]);
            $this->a_permission_admin[] = $pageRole->id;

            foreach ($roles as $key => $value) {
                $roleAdmin = Role::find($value);
                $roleAdmin->attachPermissions($this->a_permission_admin);
            }
        }
    }
}
