<?php

namespace Clavel\TimeTracker\Controllers\Activities;

use App\Http\Controllers\AdminController;
use Clavel\TimeTracker\Models\Activity;
use Clavel\TimeTracker\Models\Customer;
use Clavel\TimeTracker\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Yajra\DataTables\Facades\DataTables;

class AdminActivitiesController extends AdminController
{
    protected $page_title_icon = '<i class="fa fa-list" aria-hidden="true"></i>';

    public function __construct()
    {
        parent::__construct();

        $this->access_permission = 'admin-activities';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!Auth::user()->can('admin-activities-list')) {
            app()->abort(403);
        }

        $page_title = trans("timetracker::activities/admin_lang.title");

        return view('timetracker::activities.admin_index', compact('page_title'))
            ->with('page_title_icon', $this->page_title_icon);
    }

    public function getData()
    {
        $activities = Activity::select(
            array(
                'activities.id',
                'activities.active',
                'activities.name'
            )
        );

        return Datatables::of($activities)
            ->editColumn('active', function ($data) {
                return '<button class="btn '.($data->active?"btn-success":"btn-danger").' btn-sm" '.
                    (auth()->user()->can("admin-activities-update")?"onclick=\"javascript:changeStatus('".
                        url('admin/activities/state/'.$data->id)."');\"":"").'
                        data-content="'.($data->active?
                        trans('general/admin_lang.descativa'):
                        trans('general/admin_lang.activa')).'"
                        data-placement="right" data-toggle="popover">
                        <i class="fa '.($data->active?"fa-eye":"fa-eye-slash").'" aria-hidden="true"></i>
                        </button>';
            })
            ->addColumn('actions', function ($data) {
                $actions = '';
                if (auth()->user()->can("admin-activities-update")) {
                    $actions .= '<button class="btn btn-primary btn-sm" onclick="javascript:window.location=\'' .
                        url('admin/activities/' . $data->id . '/edit') . '\';" data-content="' .
                        trans('general/admin_lang.modificar') . '" data-placement="right" data-toggle="popover">
                        <i class="fa fa-pencil" aria-hidden="true"></i></button> ';
                }
                if (auth()->user()->can("admin-activities-delete")) {
                    $actions .= '<button class="btn btn-danger btn-sm" onclick="javascript:deleteElement(\''.
                        url('admin/activities/'.$data->id).'\');" data-content="'.
                        trans('general/admin_lang.borrar').'" data-placement="left" data-toggle="popover">
                        <i class="fa fa-trash" aria-hidden="true"></i></button>';
                }
                return $actions;
            })
            ->removeColumn('id', 'customer_id', 'activity_id')
            ->rawColumns(['active', 'actions'])
            ->make();
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!Auth::user()->can('admin-activities-create')) {
            app()->abort(403);
        }

        $activity = new Activity();
        $form_data = array('route' => array('admin.activities.store'),
            'method' => 'POST',
            'id' => 'formData',
            'class' => 'form-horizontal');
        $page_title = trans("timetracker::activities/admin_lang.new");


        return view('timetracker::activities.admin_edit', compact('page_title', 'activity', 'form_data'))
            ->with('page_title_icon', $this->page_title_icon);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!Auth::user()->can('admin-activities-create')) {
            app()->abort(403);
        }

        $activity = new Activity();
        $this->saveData($activity, $request);

        return redirect('admin/activities/'.$activity->id."/edit")
            ->with('success', trans('general/admin_lang.save_ok'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Activity  $activity
     * @return \Illuminate\Http\Response
     */
    public function show(Activity $activity)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  integer $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!Auth::user()->can('admin-activities-update')) {
            app()->abort(403);
        }

        $activity = Activity::find($id);
        $form_data = array('route' => array('admin.activities.update', $activity->id),
            'method' => 'PATCH',
            'id' => 'formData',
            'class' => 'form-horizontal');
        $page_title = trans("timetracker::activities/admin_lang.modify");


        return view('timetracker::activities.admin_edit', compact('page_title', 'activity', 'form_data'))
            ->with('page_title_icon', $this->page_title_icon);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  integer  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (!Auth::user()->can('admin-activities-update')) {
            app()->abort(403);
        }

        $activity = Activity::find($id);
        if (empty($activity)) {
            abort(404);
        }
        $this->saveData($activity, $request);

        return redirect('admin/activities/'.$activity->id."/edit")
            ->with('success', trans('general/admin_lang.save_ok'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  integer  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!Auth::user()->can('admin-activities-delete')) {
            app()->abort(403);
        }

        $activity = Activity::find($id);
        if (empty($activity)) {
            abort(404);
        }
        $activity->delete();


        return response()->json(array(
            'success' => true,
            'msg' => trans("timetracker::activities/admin_lang.deleted"),
            'id' => $activity->id
        ));
    }


    public function setChangeState($id)
    {
        if (!Auth::user()->can('admin-activities-update')) {
            app()->abort(403);
        }

        $activity = Activity::find($id);

        if (!empty($activity)) {
            $activity->active = !$activity->active;
            return $activity->save()?1:0;
        }

        return 0;
    }

    private function saveData(Activity $activities, Request $request)
    {
        $activities->name = $request->get("name", "");
        $activities->description = $request->get("description", "");
        $activities->color = $request->get("color", "");

        $activities->fixed_rate = $request->get("fixed_rate", "");
        $activities->hourly_rate = $request->get("hourly_rate", "");
        $activities->active = $request->get("active", false);
        $activities->save();
    }
}
