<?php

namespace Clavel\NotificationBroker\Controllers\Notifications;

use App\Http\Controllers\AdminController;
use Clavel\NotificationBroker\Models\Notification;
use Clavel\NotificationBroker\Models\NotificationStatus;
use App\Services\StoragePathWork;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class AdminNotificationsController extends AdminController
{
    public function __construct()
    {
        parent::__construct();

        $this->access_permission = 'admin-notifications-broker';
    }

    public function index(Request $request)
    {
        if (!auth()->user()->can('admin-notifications-broker-list')) {
            abort(403);
        }

        $dt = $request->get("date_ini", "");
        $only_certified = $request->get("only_certified", false);

        if (empty($dt)) {
            $fecha = Carbon::today()->subDays(7);
        } else {
            try {
                $fecha = Carbon::createFromFormat('d/m/Y', $dt, "Europe/Madrid")->startOfDay();
            } catch (\Exception $ex) {
                $fecha = Carbon::today()->subDays(7);
            }
        }

        $date_ini = $fecha->format("d/m/Y");

        $page_title = trans("notificationbroker::notifications/admin_lang.notifications_log");
        return view(
            "notificationbroker::notifications.admin_index",
            compact(
                'page_title',
                'date_ini',
                'only_certified'
            )
        );
    }


    public function getData(Request $request)
    {
        if (!auth()->user()->can('admin-notifications-broker-list')) {
            abort(403);
        }


        $dt = $request->get("date_ini", "");
        $only_certified = $request->get("only_certified", false);

        if (empty($dt)) {
            $fecha = Carbon::today()->subDays(7);
        } else {
            try {
                $fecha = Carbon::createFromFormat('d/m/Y', $dt, "Europe/Madrid")->startOfDay();
            } catch (\Exception $ex) {
                $fecha = Carbon::today()->subDays(7);
            }
        }

        $date_ini = $fecha->format("Y-m-d");
        $receiver_filter = '';
        $guid_filter = '';
        $slugtype_filter = '';
        $search_value = '';

        try {
            $receiver_filter = $request->get("columns")[8]["search"]["value"];
        } catch (\Exception $ee) {
        }

        try {
            $guid_filter = $request->get("columns")[4]["search"]["value"];
        } catch (\Exception $ee) {
        }

        try {
            $slugtype_filter = $request->get("columns")[3]["search"]["value"];
        } catch (\Exception $ee) {
        }

        try {
            $search_value = $request->get("search")["value"];
        } catch (\Exception $ee) {
        }

        $notificationsStatus = NotificationStatus::select('slug', 'name', 'color')
            ->orderBy('slug', 'ASC')
            ->get();

        $statusNot = array();
        foreach ($notificationsStatus as $struct) {
            $statusNot[$struct->slug] = $struct;
        }

        $notifications = Notification::select(
            array(
                'notifications_broker.id',
                'notifications_broker.receiver',
                'notifications_broker.guid',
                'notifications_broker.slug_type',
                'notifications_broker.sent_at',
                'notifications_broker.response_code',
                'notifications_broker.response_info',
                'notifications_broker.retries',
                'notifications_broker.status_slug',
                'notifications_broker.credits',
                'notifications_broker.is_certified',
                'notifications_broker.certificate_file',
                'notifications_broker.validated_at',
                //'notifications_broker.status_slug'
            )
        )
        ->whereDate('notifications_broker.created_at', '>=', $date_ini);

        if ($receiver_filter!='') {
            $notifications = $notifications->where('receiver', "like", $receiver_filter.'%');
        }

        if ($guid_filter!='') {
            $notifications = $notifications->where('guid', $guid_filter);
        }

        if ($slugtype_filter!='') {
            $notifications = $notifications->where('slug_type', 'like', $guid_filter.'%');
        }

        if ($search_value!='') {
            $notifications = $notifications->where(function ($q) use ($search_value) {
                $q->where('receiver', 'like', $search_value.'%')
                    ->orwhere("guid", 'like', $search_value.'%')
                    ->orwhere('slug_type', 'like', $search_value.'%');
            });
        }

        if ($only_certified) {
            $notifications = $notifications->where('is_certified', "=", '1');
        }

        return Datatables::of($notifications)
            ->editColumn('id', function ($row) {
                return '<button
                            id="btn_detail"
                            data-value="'.$row->id.'"
                            class="btn bg-olive btn-sm"
                            data-content="'.trans('notificationbroker::notifications/admin_lang.ver_detalle').'"
                            data-placement="right"
                            data-toggle="popover"><i class="fa fa-plus" aria-hidden="true"></i></button>';
            })
            ->editColumn('info', function ($data) {
                $actions = '';
                if (Str::startsWith($data->slug_type, 'sms')) {
                    if (!empty($data->validated_at)) {
                        $actions .= '<i class="fa fa-check-circle fa-2x" aria-hidden="true" style="color:green;"></i>';
                    } else {
                        $actions .= '<i class="fa fa-times-circle fa-2x" aria-hidden="true" style="color:red;"></i>';
                    }
                } else {
                    if (!empty($data->validated_at)) {
                        $actions .= '<i class="fa fa-check-circle fa-2x" aria-hidden="true" style="color:green;"></i>';
                    }
                }
                if (empty($actions)) {
                    $actions = '&nbsp;';
                }
                return $actions;
            })
            ->addColumn('sent_at', function ($data) {
                return $data->sentAtFormatted;
            })

            ->editColumn('receiver', function ($data) {
                return '<span class="label label-info">'
                    .$data->receiver
                    .'</span>';
            })
            /*
                ->addColumn('to' ,function ($data) {
                $payload = json_decode($data->payload, true);
                $to = $payload['to'];
                return '<span class="label label-info">'
                    .$to
                    .'</span>';
            })

            ->addColumn('subject' ,function ($data) {
                $payload = json_decode($data->payload, true);
                $subject = !empty($payload['subject'])?$payload['subject']:'';

                return $subject;

            })
            ->addColumn('content' ,function ($data) {
                $payload = json_decode($data->payload, true);
                $content = !empty($payload['content'])?$payload['content']:'';
                return strip_tags($content);
            })
            */
            ->addColumn('response_info', function ($data) {
                $response_info = !empty($data->response_info)?$data->response_info:'';
                return $response_info;
            })
            ->editColumn('slug_type', function ($data) {
                $type = explode("/", $data->slug_type);
                return '<span class="label label-warning">'
                    .$type[0]
                    .'</span>';
            })
            ->addColumn('name', function ($data) use ($statusNot) {
                $item = null;
                $resultado =  '';

                $item = $statusNot[$data->status_slug];
                if (!empty($item)) {
                    $resultado =  '<span class="label" style="background-color: '.$item->color.';">'
                        .$item['name']
                        .'</span>';
                }
                return $resultado;
            })
            ->editColumn('retries', function ($data) {
                $resultado =  '';
                if (!empty($data->retries) && $data->retries>0 && $data->response_code >= 0) {
                    $resultado =  $data->retries;
                }
                return $resultado;
            })
            ->editColumn('credits', function ($data) {
                $credits =  number_format($data->credits, 2, ",", ".");
                return $credits;
            })
            ->editColumn('actions', function ($data) {
                $actions = '';
                if ($data->is_certified && $data->status_slug != 'error') {
                    if (empty($data->certificate_file)) {
                        $actions .= '<button class="btn bg-red btn-sm"
                         data-content="' .trans('notificationbroker::notifications/admin_lang.no_certificate') . '"
                         data-placement="left" data-toggle="popover">
                    <i class="fa fa-certificate" hidden="true" aria-hidden="true"></i></button> ';
                    } else {
                        $actions .= '<button class="btn bg-green btn-sm"
                        onclick="javascript:viewCertificate(\''.
                            url('admin/notifications/viewCertificate/'.$data->id).'\');"
                        data-content="' . trans('notificationbroker::notifications/admin_lang.certificate_view') .
                            '" data-placement="left" data-toggle="popover" >
                    <i class="fa  fa-certificate"></i></button> ';
                    }
                }

                if (empty($actions)) {
                    $actions = '&nbsp;';
                }
                return $actions;
            })
            ->removeColumn('response_code')
            ->removeColumn('color')
            ->removeColumn('is_certified')
            ->removeColumn('certificate_file')
            ->removeColumn('status_slug')
            ->rawColumns(['actions', 'id', 'info', 'receiver', 'subject', 'response_info', 'slug_type', 'name'])
            ->make();
    }

    public function viewCertificate($id)
    {
        if (!auth()->user()->can('admin-notifications-broker-list')) {
            abort(403);
        }

        $notification = Notification::find($id);
        if (!empty($notification)) {
            $myServiceSPW = new StoragePathWork("");
            return $myServiceSPW->showFile($notification->certificate_file, "");
        }
        app()->abort(404);
    }

    public function getDetail(Request $request, $id)
    {
        if (!auth()->user()->can('admin-notifications-broker-list')) {
            abort(403);
        }

        $notification = Notification::where('id', $id)
            ->first();

        $payload = null;
        if (!empty($notification)) {
            $payload = json_decode($notification->payload, true);
        }

        return view("notificationbroker::notifications.admin_index_detail", compact('notification', 'payload'));
    }
}
