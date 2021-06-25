<?php

namespace App\Modules\Newsletter\Controllers;

use App\Http\Controllers\AdminController;
use App\Modules\Newsletter\Models\NewsletterMailingList;
use App\Modules\Newsletter\Requests\AdminNewsletterListsRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Yajra\DataTables\Facades\DataTables;

class AdminNewsletterListController extends AdminController
{
    protected $page_title_icon = '<i class="fa fa-list-ul" aria-hidden="true"></i>';

    public function __construct()
    {
        parent::__construct();

        $this->access_permission = 'admin-newsletter-lists';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Si no tiene permisos para ver el listado lo echa.
        if (!auth()->user()->can('admin-newsletter-lists-list')) {
            app()->abort(403);
        }

        $page_title = trans("Newsletter::admin_lang_lists.newsletter-lists");

        return view("Newsletter::admin_lists_index", compact('page_title'));
        // ->with('page_title_icon', $this->page_title_icon);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('admin-newsletter-lists-create')) {
            app()->abort(403);
        }

        $list = new NewsletterMailingList();
        $form_data = array(
            'route' => array('newsletter-lists.store'),
            'method' => 'POST',
            'id' => 'formData',
            'class' => 'form-horizontal'
        );
        $page_title = trans("Newsletter::admin_lang_lists.nueva_list");

        return view(
            'Newsletter::admin_lists_edit',
            compact(
                'page_title',
                'list',
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
    public function store(AdminNewsletterListsRequest $request)
    {
        if (!auth()->user()->can('admin-newsletter-lists-create')) {
            app()->abort(404);
        }

        $list = new NewsletterMailingList();
        $this->saveNewsletterMailingList($request, $list);

        return redirect()->to('admin/newsletter-lists/'.$list->id."/edit")
            ->with('success', trans('Newsletter::admin_lang_lists.save_ok'));
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
        if (!auth()->user()->can('admin-newsletter-lists-update')) {
            app()->abort(403);
        }

        $list = NewsletterMailingList::find($id);

        $form_data = array(
            'route' => array('newsletter-lists.update', $list->id),
            'method' => 'PATCH',
            'id' => 'formData',
            'class' => 'form-horizontal'
        );
        $page_title = trans("Newsletter::admin_lang_lists.modify_list");

        return view('Newsletter::admin_lists_edit', compact('page_title', 'list', 'form_data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AdminNewsletterListsRequest $request, $id)
    {
        if (!auth()->user()->can('admin-newsletter-lists-update')) {
            app()->abort(403);
        }

        $list = NewsletterMailingList::find($id);
        if (empty($list)) {
            app()->abort(404);
        }

        $this->saveNewsletterMailingList($request, $list);

        return redirect()->to('admin/newsletter-lists/'.$list->id."/edit")
            ->with('success', trans('Newsletter::admin_lang_lists.save_ok'));
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
        if (!auth()->user()->can('admin-newsletter-lists-delete')) {
            app()->abort(404);
        }

        $list = NewsletterMailingList::find($id);

        if (empty($list)) {
            app()->abort(500);
        }

        $list->delete();

        return Response::json(array(
            'success' => true,
            'msg' => 'Categoría eliminada',
            'id' => $list->id
        ));
    }

    public function getData()
    {
        $lists = NewsletterMailingList::get();

        return Datatables::of($lists)
            ->addColumn('campaigns', function ($data) {
                return $data->campaigns()->count();
            })
            ->addColumn('subscriptions', function ($data) {
                return $data->subscriptions()->count();
            })
            ->addColumn('actions', '
                @if(auth()->user()->can("admin-newsletter-lists-update"))
                    <button class="btn btn-primary btn-sm"
                    onclick="javascript:window.location=\'{{ url(\'admin/newsletter-lists/\'.$id.\'/edit\') }}\';"
                    data-content="'.trans('general/admin_lang.modificar').'"
                    data-placement="right" data-toggle="popover">
                    <i class="fa fa-pencil" aria-hidden="true"></i></button>
                @endif
                @if(auth()->user()->can("admin-newsletter-lists-delete"))
                    <button class="btn btn-danger btn-sm"
                    onclick="javascript:deleteElement(\'{{ url(\'admin/newsletter-lists/\'.$id.\'\') }}\');"
                    data-content="'.trans('general/admin_lang.borrar').'"
                    data-placement="left" data-toggle="popover"><i class="fa fa-trash" aria-hidden="true"></i></button>
                @endif
                ')
            ->removeColumn('id')
            ->rawColumns(['actions'])
            ->make();
    }

    private function saveNewsletterMailingList(Request $request, NewsletterMailingList $NewsletterMailingList)
    {
        $NewsletterMailingList->name = $request->input('name');
        $NewsletterMailingList->slug = str_slug($request->input('name'));
        $NewsletterMailingList->requires_opt_in = $request->input('requires_opt_in', true);

        $NewsletterMailingList->save();
    }
}
