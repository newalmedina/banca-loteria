<?php

use App\Models\User;
use App\Modules\Newsletter\Models\Newsletter;
use App\Modules\Newsletter\Models\NewsletterCampaign;
use App\Modules\Newsletter\Models\NewsletterMailingList;
use App\Modules\Newsletter\Models\NewsletterCampaignState;
use App\Modules\Newsletter\Models\NewsletterSubscription;

use App\Modules\Posts\Models\Post;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class NewsletterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //$faker = Faker::create();

        /*$connection = config('database.default');
        $this->random = "RAND()";
        if($connection == "sqlite") {
            $this->random = "abs(random())";
        }*/

        //DB::table('newsletter_subscriptions')->delete();
        DB::table('newsletter_campaign_states')->delete();
        //DB::table('newsletter_campaigns')->delete();
        DB::table('newsletter_lists')->delete();


        //DB::table('newsletter_row_fields')->delete();
        //DB::table('newsletter_rows')->delete();
        //DB::table('newsletters')->delete();




        $maestro = new NewsletterCampaignState();
        $maestro->id = 0;
        $maestro->name = "Pending";
        $maestro->class = "alert-warning";
        $maestro->active = '1';
        $maestro->code = "0";
        $maestro->save();

        $maestro = new NewsletterCampaignState();
        $maestro->id = 1;
        $maestro->name = "Prepared";
        $maestro->class = "alert-primary";
        $maestro->active = '1';
        $maestro->code = "1";
        $maestro->save();

        $maestro = new NewsletterCampaignState();
        $maestro->id = 2;
        $maestro->name = "Sending";
        $maestro->class = "alert-info";
        $maestro->active = '1';
        $maestro->code = "2";
        $maestro->save();

        $maestro = new NewsletterCampaignState();
        $maestro->id = 3;
        $maestro->name = "Completed";
        $maestro->class = "alert-success";
        $maestro->active = '1';
        $maestro->code = "3";
        $maestro->save();


        /*
                // Create newsletters
                for($i=0;$i<10;$i++) {
                    $newsletter = array(
                        'name' => $faker->bs,
                        'subject' => $faker->bs,
                        'generated' => 0,
                        'created_at' => new DateTime,
                        'updated_at' => new DateTime
                    );

                    $newsletter_id = DB::table('newsletters')->insertGetId( $newsletter );

                    for($j=0;$j<10;$j++) {

                        $cols = $faker->numberBetween(1, 3);

                        $newsletter_rows = array(
                            'newsletter_id' => $newsletter_id,
                            'cols' => $cols,
                            'position' => $j+1,
                            'created_at' => new DateTime,
                            'updated_at' => new DateTime
                        );

                        $newsletter_row_id = DB::table('newsletter_rows')->insertGetId( $newsletter_rows );


                        for($k=0;$k<$cols;$k++) {

                            $post_random = Post::select('*')->orderByRaw($this->random)->first();

                            $newsletter_row_fields = array(
                                'newsletter_row_id' => $newsletter_row_id,
                                'post_id' => $post_random->id,
                                'type' => 'post',
                                'position' => $k+1,
                                'image_position' => 'post',
                                'title_color' => $faker->hexColor,
                                'text_color' => $faker->hexColor,
                                'text_length' => (500/$cols),
                                'complete_post' => 0,
                                'created_at' => new DateTime,
                                'updated_at' => new DateTime
                            );
                            DB::table('newsletter_row_fields')->insertGetId( $newsletter_row_fields );

                        }
                    }
                }
        */
        // Create the main list
        $list = array(
            'name' => 'CMS Clavel Newsletter List',
            'slug' => 'newsletterSubscribers',
            'requires_opt_in' => false,
            'created_at' => new DateTime,
            'updated_at' => new DateTime
        );

        $idlist = DB::table('newsletter_lists')->insertGetId($list);
        /*
                // Create some additional data
                factory(NewsletterMailingList::class, 5)->create();

                factory(NewsletterCampaign::class, 25)->create();

                $newsletters = Newsletter::all();
                $lists = NewsletterMailingList::all();
                $users = User::all();

                for($i=0;$i<100;$i++) {
                    factory(NewsletterSubscription::class, 1)
                        ->create(
                            [
                                'user_id' => $users->random(1)->first()->id,
                                //'list_id' => $lists->random(1)->first()->id
                            ]
                        );
                }

                $campaigns = NewsletterCampaign::all();
                foreach ($campaigns as $campaign) {
                    //$campaign->list_id = $lists->random(1)->first()->id;
                    $campaign->newsletter_id = $newsletters->random(1)->first()->id;
                    $campaign->save();
                }*/
    }
}
