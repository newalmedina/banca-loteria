<?php

use App\Models\UserProfile;
use Clavel\Posts\Models\PostComment;
use Clavel\Posts\Models\PostTag;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('post_tags')->delete();
        DB::table('post_tag_translations')->delete();
        DB::table('post_translations')->delete();
        DB::table('posts')->delete();

        $connection = config('database.default');
        $faker = Faker::create();

        $this->random = "RAND()";
        if ($connection == "sqlite") {
            $this->random = "abs(random())";
        }

        factory('Clavel\Posts\Models\PostTagTranslation', 100)->create();
        factory('Clavel\Posts\Models\PostTranslation', 50)
            ->create()
            ->each(function ($post_translation) use ($faker) {
                $title =$faker->sentence(5);
                $post_translation_en = $post_translation->replicate();
                $post_translation_en->locale = 'en';
                $post_translation_en->title = $title;
                $post_translation_en->body =$faker->paragraph(5);
                $post_translation_en->url_seo = str_slug($title);
                $post_translation_en->save();

                for ($i=0;$i<rand(0, 3);$i++) {
                    try {
                        DB::table('post_post_tag')->insert(
                            [
                                'post_id' => $post_translation->post->id,
                                'post_tag_id' => PostTag::select('id')->orderByRaw($this->random)->first()->id,
                            ]
                        );
                    } catch (Exception $e) {
                    }
                }

                $last_comment = null;
                for ($i=0;$i<rand(0, 3);$i++) {
                    $comment = $post_translation->post->comments()->save(
                        factory(PostComment::class)->make()
                    );

                    $user_random = UserProfile::select('*')->orderByRaw($this->random)->first();
                    $comment->user = $user_random->first_name . " " . $user_random->last_name;
                    $comment->email = $user_random->user->email;
                    $comment->user_id = $user_random->user->id;
                    if (!empty($last_comment) && rand(0, 1) == 1) {
                        $comment->parent_id = $last_comment->id;
                    }
                    $comment->save();

                    $last_comment = $comment;
                }
            });
    }
}
