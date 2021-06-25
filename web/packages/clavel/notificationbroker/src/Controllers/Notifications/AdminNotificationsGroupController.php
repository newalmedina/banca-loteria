<?php

namespace Clavel\NotificationBroker\Controllers\Notifications;

use App\Http\Controllers\AdminController;
use Clavel\NotificationBroker\Models\NotificationGroup;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class AdminNotificationsGroupController extends AdminController
{
    public function __construct()
    {
        parent::__construct();

        $this->access_permission = 'admin-notifications-broker-group';
    }

    public function index()
    {
        if (!auth()->user()->can('admin-notifications-broker-group-list')) {
            abort(403);
        }
        $page_title = trans("notificationbroker::notifications-group/admin_lang.notifications_log");
        return view("notificationbroker::notifications-group.admin_index", compact('page_title'));
    }

    public function getData()
    {
        $notificationsGroup = NotificationGroup::select(
            array(
                'notifications_broker_group.id',
                'notifications_broker_group.fichero_group',
                'notifications_broker_group.created_at',
                'users.email',
                'user_profiles.first_name',
                'user_profiles.last_name',
                DB::raw('CONCAT(user_profiles.first_name," ",user_profiles.last_name," [",users.email,"]") as sender')
            )
        )
        ->leftJoin('users', 'notifications_broker_group.user_id', '=', 'users.id')
        ->leftJoin('user_profiles', 'user_profiles.user_id', '=', 'users.id');

        return Datatables::of($notificationsGroup)
            ->editColumn('created_at', function ($data) {
                $created_at = Carbon::parse($data->created_at)->format("d/m/Y H:i:s");
                return $created_at;
            })
            ->editColumn('fichero_group', function ($data) {
                try {
                    // Verificamos la existencia de la carpeta de ficheros de notificacion
                    if (Storage::disk('local')->exists("/".$data->fichero_group)) {
                        $url = "notifications-group/getFileInfo/".$data->id;
                        return '<a href="'.$url.'" target="_blank">'
                            . "/".$data->fichero_group
                            .'</a>';
                    }
                } catch (Exception $ex) {
                }

                return "/".$data->fichero_group;
            })
            ->editColumn('sender', function ($data) {
                return '<span class="label label-warning">'
                    .$data->sender
                    .'</span>';
            })
            ->filterColumn('sender', function ($query, $keyword) {
                $query->whereRaw(
                    "CONCAT(user_profiles.first_name,' ',user_profiles.last_name,' [',users.email,']') like ?",
                    ["%{$keyword}%"]
                );
            })
            ->removeColumn('id')
            ->rawColumns(['sender', 'fichero_group'])
            ->make();
    }


    public function getFileInfo(Request $request, $id)
    {
        if (!auth()->user()->can('admin-notifications-broker-group-list')) {
            abort(403);
        }

        $data = NotificationGroup::find($id);
        if (empty($data)) {
            abort(404);
        }

        if (Storage::disk('local')->exists("/".$data->fichero_group)) {
            $file = Storage::disk('local')->get("/".$data->fichero_group);
            $mimetype = Storage::disk('local')->mimeType("/".$data->fichero_group);

            return (new \Illuminate\Http\Response($file, 200))
                ->header('Content-Type', $mimetype);
        } else {
            abort(404);
        }
    }
}
