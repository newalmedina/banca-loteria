<?php

namespace Clavel\Posts\Controllers;

use App\Http\Controllers\AdminController;
use Clavel\Posts\Models\PostComment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Yajra\DataTables\Facades\DataTables;

class AdminPostCommentsController extends AdminController
{
    protected $page_title_icon = '<i class="fa fa-commenting" aria-hidden="true"></i>';

    public function __construct()
    {
        parent::__construct();

        $this->access_permission = 'admin-posts-comments';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Si no tiene permisos para ver el listado lo echa.
        if (!auth()->user()->can('admin-posts-comments-list')) {
            app()->abort(403);
        }

        $page_title = trans("posts::admin_lang.news_comments");

        return view("posts::admin_comments_index", compact('page_title'));
        //->with('page_title_icon', $this->page_title_icon);
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
        if (!auth()->user()->can('admin-posts-comments-delete')) {
            app()->abort(403);
        }

        $comment = PostComment::find($id);

        if (empty($comment)) {
            app()->abort(500);
        }

        $comment->delete();

        return Response::json(array(
            'success' => true,
            'msg' => 'Comentario eliminado',
            'id' => $comment->id
        ));
    }

    public function getData()
    {
        $locale = app()->getLocale();

        $comments = DB::table('post_comments as p')
        ->join('post_translations as pt', function ($join) use ($locale) {
            $join->on('pt.post_id', '=', 'p.post_id');
            $join->on('pt.locale', '=', DB::raw("'".$locale."'"));
        })
        ->select(
            array(
                'p.id',
                'p.user',
                'p.email',
                'p.user_id',
                'pt.title',
                'p.comment',
                'p.created_at',

            )
        )
         ->orderBy('p.created_at', 'DESC');

        return Datatables::of($comments)
            ->editColumn('comment', function ($row) {
                return nl2br($row->comment);
            })
            ->editColumn('user', function ($row) {
                return $row->user;
            })
            ->editColumn('email', function ($row) {
                return $row->email;
            })
            ->addColumn('actions', '
                @if(auth()->user()->can("admin-posts-comments-delete"))
                    <button class="btn btn-danger btn-sm"
                    onclick="javascript:deleteElement(\'{{ url(\'admin/posts/comments/\'.$id.\'\') }}\');"
                    data-content="'.trans('general/admin_lang.borrar').'"
                    data-placement="left" data-toggle="popover"><i class="fa fa-trash" aria-hidden="true"></i></button>
                @endif
                ')
            ->removeColumn('id')
            ->removeColumn('user_id')
            ->rawColumns(['actions'])
            ->make();
    }
}
