<?php

namespace Clavel\TimeTracker\Controllers\Customers;

use DateTimeZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Clavel\TimeTracker\Models\Country;
use Clavel\TimeTracker\Models\Currency;

use Clavel\TimeTracker\Models\Customer;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Controllers\AdminController;

class AdminCustomersController extends AdminController
{
    protected $page_title_icon = '<i class="fa fa-users" aria-hidden="true"></i>';

    public function __construct()
    {
        parent::__construct();

        $this->access_permission = 'admin-customers';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!Auth::user()->can('admin-customers-list')) {
            app()->abort(403);
        }

        $page_title = trans("timetracker::customers/admin_lang.title");

        return view('timetracker::customers.admin_index', compact('page_title'))
            ->with('page_title_icon', $this->page_title_icon);
    }


    public function getData()
    {
        $customers = Customer::select(
            array(
                'customers.id',
                'customers.active',
                'customers.name',
                'customers.code'
            )
        );

        return Datatables::of($customers)
            ->editColumn('active', function ($data) {
                return '<button class="btn '.($data->active?"btn-success":"btn-danger").' btn-sm" '.
                    (auth()->user()->can("admin-customers-update")?"onclick=\"javascript:changeStatus('".
                        url('admin/customers/state/'.$data->id)."');\"":"").'
                        data-content="'.($data->active?
                        trans('general/admin_lang.descativa'):
                        trans('general/admin_lang.activa')).'"
                        data-placement="right" data-toggle="popover">
                        <i class="fa '.($data->active?"fa-eye":"fa-eye-slash").'" aria-hidden="true"></i>
                        </button>';
            })
            ->addColumn('actions', function ($data) {
                $actions = '';
                if (auth()->user()->can("admin-customers-update")) {
                    $actions .= '<button class="btn btn-primary btn-sm" onclick="javascript:window.location=\'' .
                        url('admin/customers/' . $data->id . '/edit') . '\';" data-content="' .
                        trans('general/admin_lang.modificar') . '" data-placement="right" data-toggle="popover">
                        <i class="fa fa-pencil" aria-hidden="true"></i></button> ';
                }
                if (auth()->user()->can("admin-customers-delete")) {
                    $actions .= '<button class="btn btn-danger btn-sm" onclick="javascript:deleteElement(\''.
                        url('admin/customers/'.$data->id).'\');" data-content="'.
                        trans('general/admin_lang.borrar').'" data-placement="left" data-toggle="popover">
                        <i class="fa fa-trash" aria-hidden="true"></i></button>';
                }
                return $actions;
            })
            ->removeColumn('id')
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
        if (!Auth::user()->can('admin-customers-create')) {
            app()->abort(403);
        }

        $customer = new Customer();
        $form_data = array('route' => array('admin.customers.store'),
            'method' => 'POST',
            'id' => 'formData',
            'class' => 'form-horizontal');
        $page_title = trans("timetracker::customers/admin_lang.new");

        $country = new Country();
        $country_list = $country->getList(app()->getLocale());

        $currency = new Currency();
        $currency_list = $currency->getList(app()->getLocale());

        $timezone_list = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
        $customer->timezone = 343; // Defecto Europe/Madrid

        return view(
            'timetracker::customers.admin_edit',
            compact('page_title', 'customer', 'form_data', 'country_list', 'currency_list', 'timezone_list')
        )
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
        if (!Auth::user()->can('admin-customers-create')) {
            app()->abort(403);
        }

        $customer = new Customer();
        $this->saveData($customer, $request);

        return redirect('admin/customers/'.$customer->id."/edit")
            ->with('success', trans('general/admin_lang.save_ok'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function show(Customer $customer)
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
        if (!Auth::user()->can('admin-customers-update')) {
            app()->abort(403);
        }

        $customer = Customer::find($id);
        $form_data = array(
            'route' => array('admin.customers.update', $customer->id),
            'method' => 'PATCH',
            'id' => 'formData',
            'class' => 'form-horizontal');
        $page_title = trans("timetracker::customers/admin_lang.modify");


        $country = new Country();
        $country_list = $country->getList(app()->getLocale());

        $currency = new Currency();
        $currency_list = $currency->getList(app()->getLocale());

        $timezone_list = DateTimeZone::listIdentifiers(DateTimeZone::ALL);

        return view(
            'timetracker::customers.admin_edit',
            compact('page_title', 'customer', 'form_data', 'country_list', 'currency_list', 'timezone_list')
        )
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
        if (!Auth::user()->can('admin-customers-update')) {
            app()->abort(403);
        }

        $customer = Customer::find($id);
        if (empty($customer)) {
            abort(404);
        }
        $this->saveData($customer, $request);

        return redirect('admin/customers/'.$customer->id."/edit")
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
        if (!Auth::user()->can('admin-customers-delete')) {
            app()->abort(403);
        }

        $customer = Customer::find($id);
        if (empty($customer)) {
            abort(404);
        }
        $customer->delete();


        return response()->json(array(
            'success' => true,
            'msg' => trans("timetracker::customers/admin_lang.deleted"),
            'id' => $customer->id
        ));
    }


    public function setChangeState($id)
    {
        if (!Auth::user()->can('admin-customers-update')) {
            app()->abort(403);
        }

        $customer = Customer::find($id);

        if (!empty($customer)) {
            $customer->active = !$customer->active;
            return $customer->save()?1:0;
        }

        return 0;
    }

    private function saveData(Customer $customers, Request $request)
    {
        $customers->name = $request->get("name", "");
        $customers->code = $request->get("code", "");
        $customers->tax_id = $request->get("tax_id", "");
        $customers->description = $request->get("description", "");
        $customers->company = $request->get("company", "");
        $customers->contact = $request->get("contact", "");
        $customers->address = $request->get("address", "");
        $customers->country = $request->get("country", "");
        $customers->currency = $request->get("currency", "");
        $customers->phone = $request->get("phone", "");
        $customers->fax = $request->get("fax", "");
        $customers->mobile = $request->get("mobile", "");
        $customers->email = $request->get("email", "");
        $customers->homepage = $request->get("homepage", "");
        $customers->timezone = $request->get("timezone", "");
        $customers->color = $request->get("color", "");
        $customers->fixed_rate = $request->get("fixed_rate", "");
        $customers->hourly_rate = $request->get("hourly_rate", "");
        $customers->active = $request->get("active", false);
        $customers->save();
    }
}
