<?php

namespace Clavel\Posts\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\FrontController;
use Clavel\Posts\Requests\FrontPostCommentRequest;
use Clavel\Posts\Models\Post;
use Clavel\Posts\Models\PostComment;
use Clavel\Posts\Models\PostTag;
use App\Models\User;
use App\Services\StoragePathWork;
use Clavel\Posts\Services\PostService;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Response;

class FrontPostsController extends Controller
{
    protected $paginationComments = 10000;

    /*

    */

    public function index(Request $request, $selected_tag = '')
    {
        $page_title = trans("posts::front_lang.newpost");


        $posts = Post::join('post_translations', 'posts.id', '=', 'post_translations.post_id')
            ->where('post_translations.locale', app()->getLocale())
            ->ActivePosts();

        if ($selected_tag!='') {
            $posts = $posts->dataValue($selected_tag);
            $tag = PostTag::find($selected_tag);
        } else {
            $tag = "";
        }

        $q = $request->get('q', "");
        if (!empty($q)) {
            $posts = $posts->where(function ($query) use ($q) {
                $query->where('title', 'like', '%'.$q.'%')
                    ->orWhere('body', 'like', '%'.$q.'%');
            });
        }

        // Si somos invitados no dejamos ver las noticias privadas (ni si quiera el titular)
        if (auth()->guest()) {
            $posts = $posts->where('permission', 0);
        }

        $posts = $posts->orderBy("date_post", "DESC")->paginate(6);

        $tags = PostTag::Actives()->get();
        return view(
            'posts::front_index',
            compact(
                'page_title',
                'posts',
                'selected_tag',
                'tag',
                'tags',
                'q'
            )
        );
    }

    public function postsDetail(Request $request)
    {
        $post = Post::whereTranslation('url_seo', $request->slug)
            ->activePosts()
            ->first();

        if (empty($post)) {
            app()->abort(500);
        }

        // Si la página tiene permisos
        if ($post->permission>0) {
            $this->middleware("auth");

            if (auth()->user()==null) {
                return redirect()->guest('/');
            }

            // Verificamos si tiene permisos por role
            if ($post->permission == 1) {
                if (!auth()->user()->can($post->permission_name)) {
                    return redirect()->guest('/');
                }
            }
        }

        // Añadimos el track
        PostService::trackAccess($post, auth()->user());

        $user = (auth()->user()!=null) ? auth()->user() : new User();
        $form_data = array('route' => array('posts.post.comment'), 'method' => 'POST', 'id' => 'posts-form');

        $myPathWork = new StoragePathWork('users');

        $page_title = trans("posts::front_lang.post");
        $paginationComments = $this->paginationComments;

        $tags = PostTag::Actives()->limit(30)->offset(30)->get();

        return view(
            'posts::front_post_detail',
            compact(
                'page_title',
                'post',
                'user',
                'form_data',
                'myPathWork',
                'paginationComments',
                'tags'
            )
        );
    }

    public function cargaComments($pagina)
    {
        $inicio = ($this->paginationComments * ($pagina-1));

        $comentarios = PostComment::soloPadres()
            ->orderBy("created_at", "DESC")
            ->skip($inicio)
            ->take($this->paginationComments)
            ->get();
        $comentarioCount = PostComment::soloPadres()->count();

        $a_return = [];

        foreach ($comentarios as $comentario) {
            $a_return[$comentario->id]['id'] = $comentario->id;
            $a_return[$comentario->id]['usuario'] = (!is_null($comentario->user_id)) ?
                $comentario->user->user_profile->fullname : $comentario->usuario;
            $a_return[$comentario->id]['photo'] = ((!is_null($comentario->user_id))
                && $comentario->user->user_profile->photo!='') ? $comentario->user->user_profile->photo : "";
            $a_return[$comentario->id]['comentario'] = $comentario->comentario;
            $a_return[$comentario->id]['created_at'] = $comentario->date_comment_formatted;
            if ($comentario->hijos($comentario->id)->count()>0) {
                $a_return[$comentario->id]["haschild"] = true;
                foreach ($comentario->hijos($comentario->id)->orderBy("created_at", "ASC")->get() as $reply) {
                    $a_return[$comentario->id]["child"][$reply->id]['id'] = $reply->id;
                    $a_return[$comentario->id]["child"][$reply->id]['usuario'] = (!is_null($reply->user_id)) ?
                        $reply->user->user_profile->fullname : $reply->usuario;
                    $a_return[$comentario->id]["child"][$reply->id]['photo'] = ((!is_null($reply->user_id)) &&
                        $reply->user->user_profile->photo!='') ? $reply->user->user_profile->photo : "";
                    $a_return[$comentario->id]["child"][$reply->id]['comentario'] = $reply->comentario;
                    $a_return[$comentario->id]["child"][$reply->id]['created_at'] = $reply->date_comment_formatted;
                }
            } else {
                $a_return[$comentario->id]["haschild"] = false;
            }
        }

        $a_return["showButton"] = ($comentarioCount>($this->paginationComments*$pagina)) ? true : false;

        return Response::json($a_return);
    }

    public function postComment(FrontPostCommentRequest $request)
    {
        $post = Post::find($request->input("post_id"));
        if (!empty($post)) {
            $comentario = new PostComment();

            $comentario->parent_id = ($request->input("parent_id")!='') ? $request->input("parent_id") : 0;
            $comentario->post_id = $request->input("post_id");
            if (auth()->guest()) {
                $comentario->user = $request->input("fullname");
                $comentario->email = $request->input("email");
            } else {
                $comentario->user_id =auth()->user()->id;
                $comentario->user =auth()->user()->userProfile->fullname;
                $comentario->email =auth()->user()->email;
            }

            $comentario->comment = $request->input("message");
            $comentario->save();

            return redirect()->to('posts/post/'.$post->url_seo)
                ->with('success', trans('posts::front_lang.comment_ok'));
        } else {
            return redirect()->to('posts/post/'.$post->url_seo)
                ->with('error', trans('posts::front_lang.comment_ko'));
        }
    }
}
