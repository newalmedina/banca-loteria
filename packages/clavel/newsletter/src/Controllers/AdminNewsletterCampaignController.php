<?php

namespace App\Modules\Newsletter\Controllers;

use App\Http\Controllers\AdminController;
use App\Models\Role;
use App\Models\User;
use App\Modules\Newsletter\Jobs\NewsletterSendCampaign;
use App\Modules\Newsletter\Models\Newsletter;
use App\Modules\Newsletter\Models\NewsletterCampaign;
use App\Modules\Newsletter\Models\NewsletterCampaignState;
use App\Modules\Newsletter\Models\NewsletterMailingList;
use App\Modules\Newsletter\Models\NewsletterMailingRecipients;
use App\Modules\Newsletter\Requests\AdminNewsletterCampaignsRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Yajra\DataTables\Facades\DataTables;

class AdminNewsletterCampaignController extends AdminController
{
    protected $page_title_icon = '<i class="fa fa-bullhorn" aria-hidden="true"></i>';


    public function __construct()
    {
        parent::__construct();

        $this->access_permission = 'admin-newsletter-campaigns';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Si no tiene permisos para ver el listado lo echa.
        if (!auth()->user()->can('admin-newsletter-campaigns-list')) {
            app()->abort(403);
        }

        $page_title = trans("Newsletter::admin_lang_campaigns.newsletter-campaigns");

        $roles = Role::where('active', '=', '1')->get();

        return view("Newsletter::admin_campaigns_index", compact('page_title', 'roles'));
        // ->with('page_title_icon', $this->page_title_icon);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('admin-newsletter-campaigns-create')) {
            app()->abort(403);
        }

        $campaign = new NewsletterCampaign();
        $form_data = array(
            'route' => array('newsletter-campaigns.store'),
            'method' => 'POST',
            'id' => 'formData',
            'class' => 'form-horizontal'
        );
        $page_title = trans("Newsletter::admin_lang_campaigns.new_campaign");
        $lists = NewsletterMailingList::all();
        $newsletters = Newsletter::where("generated", "=", 1)->get();

        return view(
            'Newsletter::admin_campaigns_edit',
            compact(
                'page_title',
                'campaign',
                'form_data',
                'lists',
                'newsletters'
            )
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AdminNewsletterCampaignsRequest $request)
    {
        if (!auth()->user()->can('admin-newsletter-campaigns-create')) {
            app()->abort(404);
        }

        $campaign = new NewsletterCampaign();
        $this->saveNewsletterCampaign($request, $campaign);

        return redirect()->to('admin/newsletter-campaigns/' . $campaign->id . "/edit")
            ->with('success', trans('Newsletter::admin_lang_campaigns.save_ok'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!auth()->user()->can('admin-newsletter-campaigns-list')) {
            app()->abort(403);
        }

        $campaign = NewsletterCampaign::find($id);

        if (empty($campaign)) {
            app()->abort(500);
        }
        $page_title = trans("Newsletter::admin_lang_campaigns.campaign_stats");


        $stats_recipients = NewsletterMailingRecipients::where('campaign_id', $campaign->id)
            ->select([
                'is_sent',
                DB::raw('count(is_sent) as cuantos')
            ])
            ->groupBy('is_sent')
            ->get();

        $stats = [
            [
                "count" => 0,
                "perc" => 0
            ],
            [
                "count" => 0,
                "perc" => 0
            ],
            [
                "count" => 0,
                "perc" => 0
            ],
        ];

        $recipients_total = 0;
        foreach ($stats_recipients as $stats_recipient) {
            $stats[$stats_recipient->is_sent]["count"] = $stats_recipient->cuantos;
            $recipients_total += $stats_recipient->cuantos;
        }
        if ($recipients_total > 0) {
            for ($i = 0; $i < count($stats); $i++) {
                $stats[$i]["perc"] = round(($stats[$i]["count"] * 100) / $recipients_total, 1);
            }
        }

        return view('Newsletter::admin_campaigns_show', compact('page_title', 'campaign', 'stats'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!auth()->user()->can('admin-newsletter-campaigns-update')) {
            app()->abort(403);
        }

        $campaign = NewsletterCampaign::find($id);

        $form_data = array(
            'route' => array('newsletter-campaigns.update', $campaign->id),
            'method' => 'PATCH',
            'id' => 'formData',
            'class' => 'form-horizontal'
        );
        $page_title = trans("Newsletter::admin_lang_campaigns.modify_campaign");
        $lists = NewsletterMailingList::all();
        $newsletters = Newsletter::where("generated", "=", 1)->get();

        return view('Newsletter::admin_campaigns_edit', compact(
            'page_title',
            'campaign',
            'form_data',
            'lists',
            'newsletters'
        ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AdminNewsletterCampaignsRequest $request, $id)
    {
        if (!auth()->user()->can('admin-newsletter-campaigns-update')) {
            app()->abort(403);
        }

        $campaign = NewsletterCampaign::find($id);
        if (empty($campaign)) {
            app()->abort(404);
        }

        $this->saveNewsletterCampaign($request, $campaign);

        return redirect()->to('admin/newsletter-campaigns/' . $campaign->id . "/edit")
            ->with('success', trans('Newsletter::admin_lang_campaigns.save_ok'));
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
        if (!auth()->user()->can('admin-newsletter-campaigns-delete')) {
            app()->abort(404);
        }

        $campaign = NewsletterCampaign::find($id);

        if (empty($campaign)) {
            app()->abort(500);
        }

        $campaign->delete();

        return Response::json(array(
            'success' => true,
            'msg' => 'Campaña eliminada',
            'id' => $campaign->id
        ));
    }


    public function prepare(Request $request, $id)
    {
        if (!auth()->user()->can('admin-newsletter-campaigns-create')) {
            app()->abort(403);
        }

        $campaign = NewsletterCampaign::findOrFail($id);
        $campaign->recipients()->delete();
        $recipient_type = $request->get("newsletter_send_type", "");

        switch ($recipient_type) {
            case '0':
                // Todos los usuarios
                $recipients = User::where('active', '=', '1')->get();
                foreach ($recipients as $recipient) {
                    $subscriber = [
                        'campaign_id' => $campaign->id,
                        'user_id' => $recipient->id,
                        'is_sent' => false,
                        'sent_result' => ''
                    ];
                    NewsletterMailingRecipients::create($subscriber);
                }
                break;
            case '1':
                // Usuarios suscritos a las listas de las campañas
                $lists = $campaign->mailingList;

                foreach ($lists as $list) {
                    $recipients = $list->getCurrentSubscriptions();
                    foreach ($recipients as $recipient) {
                        $subscribers[$recipient->subscriber->id] = [
                            'campaign_id' => $campaign->id,
                            'user_id' => $recipient->subscriber->id,
                            'is_sent' => false,
                            'sent_result' => ''
                        ];
                    }
                }

                foreach ($subscribers as $subscriber) {
                    NewsletterMailingRecipients::create($subscriber);
                }

                break;
            case '2':
                // Usuarios con un rol especifico
                $roles = $request->get("newsletter_roles", []);
                $recipients = User::where("active", "1")->whereHas('roles', function ($q) use ($roles) {
                    // No role selected
                    $q->where('id', -1);
                    foreach ($roles as $role) {
                        $q->orWhere('id', $role);
                    }
                })->get();

                foreach ($recipients as $recipient) {
                    $subscriber = [
                        'campaign_id' => $campaign->id,
                        'user_id' => $recipient->id,
                        'is_sent' => false,
                        'sent_result' => ''
                    ];
                    NewsletterMailingRecipients::create($subscriber);
                }

                break;
        }

        return redirect()->to('admin/newsletter-campaigns')
            ->with('success', trans('Newsletter::admin_lang_campaigns.save_ok'));
    }


    public function send(Request $request, $id)
    {
        if (!auth()->user()->can('admin-newsletter-campaigns-create')) {
            app()->abort(403);
        }
        $campaign = NewsletterCampaign::findOrFail($id);
        $sending_state = NewsletterCampaignState::where('code', 1)->first();
        $campaign->newsletter_campaign_state_id = $sending_state->id;
        $campaign->save();

        if ($campaign->is_scheduled == '1') {
            $date_send = new Carbon($campaign->scheduled_for);
            return Response::json(array(
                'success' => true,
                'msg' => 'Campaña preparada, se enviará automáticamente el ' . $date_send->format("d/m/Y H:i"),
                'id' => $campaign->id
            ));
        }

        return Response::json(array(
            'success' => true,
            'msg' => 'Campaña preparada y enviándose',
            'id' => $campaign->id
        ));
    }



    public function getData()
    {
        $campaigns = NewsletterCampaign::get();

        return Datatables::of($campaigns)
            ->addColumn('status', function ($data) {
                $data_return = '';
                if ($data->is_sent) {
                    $data_return .= '<i class="fa fa-check-circle-o fa-2x text-success" aria-hidden="true" title="' .
                        trans('general/admin_lang_campaigns.sent') . '"></i>';
                } elseif ($data->is_scheduled) {
                    $data_return .= '<i class="fa fa-clock-o fa-2x text-warning" aria-hidden="true" title="' .
                        trans('general/admin_lang_campaigns.screduled') . '"></i>';
                }

                return $data_return;
            })
            ->addColumn('mailing_list', function ($data) {
                $strReturn = "";
                foreach ($data->mailingList as $list) {
                    if ($strReturn != "") {
                        $strReturn .= " / ";
                    }
                    $strReturn .= $list->name;
                }
                return $strReturn;
            })
            ->addColumn('newsletter', function ($data) {
                return $data->newsletter->name;
            })
            ->addColumn('actions', function ($data) {
                $data_return = '';

                if (auth()->user()->can("admin-newsletter-campaigns-update") && $data->is_pending) {
                    if ($data->recipients()->count() > 0) {
                        $data_return .= '<button class="btn bg-teal btn-sm"
                            onclick="javascript:sendCampaign
                            (\'' . url('admin/newsletter-campaigns/' . $data->id) . '/send\');"
                            data-content="' . trans('Newsletter::admin_lang_campaigns.campaign_send') . '"
                            data-placement="left" data-toggle="popover">
                            <i class="fa fa-play" aria-hidden="true"></i></button>&nbsp;';
                    }

                    $data_return .= '<button class="btn bg-purple btn-sm"
                        onclick="javascript:prepareNewsletterPopup(\'' . $data->id . '\');"
                        data-content="' . trans('Newsletter::admin_lang_campaigns.campaign_prepare') . '"
                        data-placement="right" data-toggle="popover"><i class="fa fa-address-book-o"
                        aria-hidden="true"></i></button>&nbsp;';

                    $data_return .= '<button class="btn btn-primary btn-sm"
                        onclick="javascript:window.location=\'' .
                        url('admin/newsletter-campaigns/' .
                            $data->id . '/edit') . '\';"
                        data-content="' . trans('Newsletter::admin_lang_campaigns.modificar') . '"
                        data-placement="right" data-toggle="popover"><i class="fa fa-pencil"
                        aria-hidden="true"></i></button>&nbsp;';
                }

                if (auth()->user()->can("admin-newsletter-campaigns-delete")  && $data->is_pending) {
                    $data_return .= '<button class="btn btn-danger btn-sm"
                        onclick="javascript:deleteElement(\'' .
                        url('admin/newsletter-campaigns/' . $data->id) . '\');"
                        data-content="' . trans('Newsletter::admin_lang_campaigns.borrar') . '"
                        data-placement="left" data-toggle="popover"><i class="fa fa-trash"
                        aria-hidden="true"></i></button>&nbsp;';
                }


                if (auth()->user()->can("admin-newsletter-campaigns-update") && $data->is_sent) {
                    $data_return .= '<button class="btn btn-success btn-sm"
                    onclick="javascript:window.location=\'' . url('admin/newsletter-campaigns/' . $data->id) . '\';"
                    data-content="' . trans('Newsletter::admin_lang_campaigns.show') . '"
                    data-placement="right" data-toggle="popover"><i class="fa fa-eye"
                    aria-hidden="true"></i></button>&nbsp;';
                }

                if (auth()->user()->can("admin-newsletter-campaigns-create") && $data->is_sent) {
                    $data_return .= '<button class="btn bg-maroon btn-sm"
                    onclick="javascript:window.location=\'' . url('admin/newsletter-campaigns/' .
                        $data->id . '/duplicate') . '\';"
                    data-content="' . trans('Newsletter::admin_lang_campaigns.duplicate') . '"
                    data-placement="right" data-toggle="popover"><i class="fa fa-clone"
                    aria-hidden="true"></i></button>&nbsp;';
                }

                if (auth()->user()->can("admin-newsletter-campaigns-update")
                    && ($data->is_sending || $data->is_prepared)
                ) {
                    $data_return .= '<button class="btn bg-yellow btn-sm"
                    onclick="javascript:window.location=\'' . url('admin/newsletter-campaigns/' . $data->id) . '\';"
                    data-content="' . trans('Newsletter::admin_lang_campaigns.show') . '"
                    data-placement="right" data-toggle="popover"><i class="fa fa-eye"
                    aria-hidden="true"></i></button>&nbsp;';
                }


                return $data_return;
            })
            ->removeColumn('id')
            ->rawColumns(['status', 'actions'])
            ->make();
    }

    private function saveNewsletterCampaign(Request $request, NewsletterCampaign $campaign)
    {
        $campaign->name = $request->input('name');
        $campaign->newsletter_id = $request->input('newsletter_id');
        $campaign->is_scheduled = $request->input('is_scheduled', false);

        if ($campaign->is_scheduled) {
            $campaign->scheduled_for =
                ($request->input('scheduled_for_date') &&
                    $request->input('scheduled_for_time')) ?
                Carbon::createFromFormat('d/m/Y H:i', $request->input('scheduled_for_date') . " " .
                    $request->input('scheduled_for_time')) :
                null;
        } else {
            $campaign->scheduled_for = null;
        }
        $campaign->save();

        $list = $request->input('list_id');
        if (is_null($request->input('list_id'))) {
            $list = [];
        }
        $campaign->mailingList()->sync($list);
    }

    public function duplicate($campaign_id)
    {
        if (!auth()->user()->can('admin-newsletter-campaigns-create')) {
            app()->abort(403);
        }
        $sending_state = NewsletterCampaignState::where('code', 0)->first();

        $campaign = NewsletterCampaign::findOrFail($campaign_id);
        $campaign_new = $campaign->replicate();
        $campaign_new->name = $campaign_new->name . " (cloned)";
        $campaign_new->newsletter_campaign_state_id = $sending_state->id;
        $campaign_new->sent_count = null;
        $campaign_new->sent_at = null;
        $campaign_new->save();

        $a_list = array();
        foreach ($campaign->mailingList as $list) {
            $a_list[] = $list->id;
        }
        $campaign_new->mailingList()->sync($a_list);

        return redirect("admin/newsletter-campaigns");
    }

    public function getSentList($campaign_id)
    {
        $recipints = NewsletterMailingRecipients::select(array(
            "user_profiles.first_name",
            "user_profiles.last_name",
            "is_sent", "users.email", "sent_result"
        ))
            ->join("users", "users.id", "=", "newsletter_campaign_recipients.user_id")
            ->join("user_profiles", "users.id", "=", "user_profiles.user_id")
            ->where("campaign_id", "=", $campaign_id);
        return Datatables::of($recipints)
            ->editColumn('is_sent', function ($row) {
                $divIni = "<div style='text-align:center'>";
                $divEnd = "<div style='text-align:center'>";
                if ($row->is_sent == '1') {
                    return $divIni . "<i class='fa fa-check-circle fa-2x text-success'></i>" . $divEnd;
                }
                if ($row->sent_result != '') {
                    return $divIni . "<i class='fa fa-times-circle fa-2x text-danger' data-content='" .
                        $row->sent_result . "' data-placement='right' data-toggle='popover'></i>" . $divEnd;
                }
                return "<div class='spinner '><i class='fa fa-spinner text-warning fa-spin'></i>" . $divEnd;
            })
            ->removeColumn("sent_result")
            ->rawColumns(['is_sent'])
            ->make();
    }
}
