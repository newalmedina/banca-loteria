<?php namespace Clavel\Posts\Services;

use App\Models\User;
use Carbon\Carbon;
use Clavel\Posts\Models\Post;
use Clavel\Posts\Models\PostStat;
use Clavel\Posts\Models\PostTrack;

class PostService
{
    public static function trackAccess(Post $post, User $user = null)
    {
        // Protegemos por si viene a nulo
        if (empty($post)) {
            return;
        }

        // Si es usuario registrado marcamos la visita y el número de veces que ha repetido
        if (!empty($user)) {
            $track = PostTrack::firstOrNew(array('user_id' => $user->id, 'post_id' => $post->id));
            $track->visits += 1;
            $track->save();
        }

        // Añadimos al contador general de estadisticas el acceso
        $trackStat = PostStat::firstOrNew(array('post_id' => $post->id, 'fecha' => Carbon::now()->format('Y-m-d')));
        $trackStat->visits += 1;
        $trackStat->save();
    }
}
