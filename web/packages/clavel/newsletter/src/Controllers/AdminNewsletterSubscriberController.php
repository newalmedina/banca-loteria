<?php

namespace App\Modules\Newsletter\Controllers;

use App\Http\Controllers\FrontController;
use App\Models\UserProfileData;
use App\Modules\Newsletter\Models\NewsletterMailingList;
use App\Modules\Newsletter\Models\NewsletterSubscriber;
use App\Modules\Newsletter\Models\NewsletterSubscription;
use App\Modules\Newsletter\Requests\AdminNewsletterSubscriberRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\Facades\Excel;
use Webpatser\Uuid\Uuid;
use Yajra\DataTables\Facades\DataTables;

class AdminNewsletterSubscriberController extends FrontController
{
    protected $page_title_icon = '<i class="fa fa-users" aria-hidden="true"></i>';


    public function __construct()
    {
        parent::__construct();

        $this->access_permission = 'admin-newsletter-subscribers';
    }

    public function index()
    {
        // Si no tiene permisos para ver el listado lo echa.
        if (!auth()->user()->can('admin-newsletter-subscribers-list')) {
            app()->abort(403);
        }

        $page_title = trans("Newsletter::admin_lang.newsletter-subscribers");

        return view("Newsletter::admin_subscribers_index", compact('page_title'));
        //->with('page_title_icon', $this->page_title_icon);
    }

    public function edit($id)
    {
        if (!auth()->user()->can('admin-newsletter-subscribers-update')) {
            app()->abort(403);
        }

        $subscriber = NewsletterSubscriber::findOrFail($id);

        $form_data = array(
            'route' => array('newsletter-subscribers.update', $subscriber->id),
            'method' => 'PATCH',
            'id' => 'formData',
            'class' => 'form-horizontal'
        );
        $page_title = trans("Newsletter::admin_lang.modify_subscription");
        $lists = NewsletterMailingList::all();

        return view(
            'Newsletter::admin_subscriber_edit',
            compact(
                'page_title',
                'subscription',
                'form_data',
                'subscriber',
                'lists'
            )
        );
    }

    public function update(AdminNewsletterSubscriberRequest $request, $id)
    {
        if (!auth()->user()->can('admin-newsletter-subscribers-update')) {
            app()->abort(403);
        }

        $subscriber = NewsletterSubscriber::findOrFail($id);
        $subscriber->subscriptions()->delete();

        collect($request->input('lists', []))->map(function ($list_id) use ($subscriber) {
            return NewsletterSubscription::create([
                'list_id' => $list_id,
                'user_id' => $subscriber->id,
                'opted_in' => true,
                'opted_in_at' => Carbon::now()
            ]);
        });

        $subscriber->email = $request->input("email");
        $subscriber->save();

        $subscriber->userProfile->first_name = $request->input('subscriptor_name');
        $subscriber->userProfile->last_name = $request->input('subscriptor_surname');
        $subscriber->userProfile->gender = $request->input('subscriptor_gender');
        $subscriber->userProfile->user_lang = $request->input("subscriptor_user_lang");
        $subscriber->userProfile->save();

        return redirect()->to('admin/newsletter-subscribers/' . $subscriber->id . "/edit")
            ->with('success', trans('Newsletter::admin_lang.save_ok'));
    }

    public function destroy($id)
    {
        if (!auth()->user()->can('admin-newsletter-subscribers-delete')) {
            app()->abort(403);
        }

        $suscriptions = NewsletterSubscription::where("user_id", "=", $id)->get();

        if (empty($suscriptions)) {
            app()->abort(500);
        }

        foreach ($suscriptions as $suscription) {
            $suscription->delete();
        }

        return Response::json(array(
            'success' => true,
            'msg' => 'Suscripciones eliminadas',
            'id' => $id
        ));
    }

    public function getData()
    {
        $subscribers =  DB::table('users as u')
            ->join('user_profiles as up', 'up.user_id', '=', 'u.id')
            ->leftJoin('newsletter_subscriptions as s', 'u.id', '=', 's.user_id')
            ->groupBy(
                'u.id',
                'u.email',
                'up.first_name',
                'up.last_name'
            )
            ->select(
                array(
                    'u.id',
                    'u.email',
                    'up.first_name',
                    'up.last_name',
                    DB::raw('count(s.user_id) as suscriptions')
                )
            );


        return Datatables::of($subscribers)
            ->editColumn('first_name', function ($row) {
                return $row->first_name;
            })
            ->editColumn('last_name', function ($row) {
                return $row->last_name;
            })
            ->editColumn('email', function ($row) {
                return $row->email;
            })
            ->editColumn('suscriptions', function ($row) {
                return $row->suscriptions;
            })
            ->addColumn('actions', function ($data) {
                $actions = '';
                if (auth()->user()->can("admin-newsletter-subscribers-update")) {
                    $actions .= '<button class="btn btn-primary btn-sm" onclick="javascript:window.location=\'' .
                        url('admin/newsletter-subscribers/' . $data->id . '/edit') . '\';" data-content="' .
                        trans('general/admin_lang.modificar') . '" data-placement="right" data-toggle="popover">
                        <i class="fa fa-pencil" aria-hidden="true"></i></button> ';
                }
                if (auth()->user()->can("admin-newsletter-subscribers-delete")) {
                    $actions .= '<button class="btn btn-danger btn-sm" onclick="javascript:deleteElement(\'' .
                        url('admin/newsletter-subscribers/' . $data->id) . '\');" data-content="' .
                        trans('general/admin_lang.borrar') . '" data-placement="left" data-toggle="popover">
                        <i class="fa fa-trash" aria-hidden="true"></i></button>';
                }
                return $actions;
            })
            ->removeColumn('id')
            ->rawColumns(['actions'])
            ->make();
    }

    public function generateExcel()
    {
        $file_name = trans('Newsletter::admin_lang.newsletter-subscribers');

        Excel::create($file_name, function ($excel) {

            // Título del excel
            $excel->setTitle(trans('Newsletter::admin_lang.newsletter-subscribers'));

            // Creadores
            $excel->setCreator(config('app.name', ''))
                ->setCompany(config('app.name', ''));

            // Descripción
            $excel->setDescription(trans('Newsletter::admin_lang.newsletter-subscribers'));

            $excel->sheet(trans('Newsletter::admin_lang.subscriber_subscriptions'), function ($sheet) {
                $sheet->setOrientation('landscape');

                $sheet->row(1, array(
                    trans('Newsletter::admin_lang.subscriber_identificador'),
                    trans('Newsletter::admin_lang.subscriber_name'),
                    trans('Newsletter::admin_lang.subscriber_surname'),
                    trans('Newsletter::admin_lang.subscriber_genero_sexusal'),
                    trans('Newsletter::admin_lang.subscriber_idioma'),
                    trans('Newsletter::admin_lang.subscriber_email'),
                    trans('Newsletter::admin_lang.subscriber_usuario'),
                    trans('Newsletter::admin_lang.subscriber_fecha_alta'),
                    trans('Newsletter::admin_lang.subscriber_ACTIVAR_USUARIO_USUARIO_ESTA'),
                    trans('Newsletter::admin_lang.subscriber_CONFIRMAR_USUARIO_USUARIO_ESTA')
                ));

                $sheet->row(1, function ($row) {
                    $row->setBackground('#C0C0C0');
                    $row->setFontColor('#000000');
                    $row->setFontFamily('Calibri');
                    $row->setFontSize(12);
                    $row->setFontWeight('bold');
                    $row->setAlignment('center');
                });

                $users = NewsletterSubscription::get()->unique("user_id");

                $nX = 2;
                foreach ($users as $key => $value) {
                    $fisrt_name = $value->subscriber->userProfile->first_name;
                    $last_name = $value->subscriber->userProfile->last_name;

                    $sheet->row($nX, array(
                        $value->subscriber->id,
                        $fisrt_name,
                        $last_name,
                        ($value->subscriber->userProfile->gender == 'male') ?
                            trans('Newsletter::admin_lang.subscriber_hombre') :
                            trans('Newsletter::admin_lang.subscriber_mujer'),
                        $value->subscriber->userProfile->user_lang,
                        $value->subscriber->email,
                        $value->subscriber->username,
                        $value->subscriber->created_at_formatted,
                        ($value->subscriber->active == '1') ?
                            trans('general/admin_lang.yes') :
                            trans('general/admin_lang.no'),
                        ($value->subscriber->confirmed == '1') ?
                            trans('general/admin_lang.yes') :
                            trans('general/admin_lang.no')
                    ));

                    $sheet->row($nX, function ($row) {
                        $row->setFontFamily('Calibri');
                        $row->setFontSize(12);
                    });
                    $nX++;
                }
            });
        })->export('xls');
    }
}
