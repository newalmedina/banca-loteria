<?php

use App\Modules\Events\Models\EventTag;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('event_tags')->delete();
        DB::table('event_tag_translations')->delete();
        DB::table('event_translations')->delete();
        DB::table('events')->delete();

        $connection = config('database.default');

        $this->random = "RAND()";
        if ($connection == "sqlite") {
            $this->random = "abs(random())";
        }


        factory('App\Modules\Events\Models\EventTagTranslation', 5)->create();
        factory('App\Modules\Events\Models\EventTranslation', 2)
            ->create()
            ->each(function ($event_translation) {
                for ($i=0;$i<rand(0, 3);$i++) {
                    try {
                        DB::table('event_event_tag')->insert(
                            [
                                'event_id' => $event_translation->event->id,
                                'event_tag_id' => EventTag::select('id')->orderByRaw($this->random)->first()->id,
                            ]
                        );
                    } catch (Exception $e) {
                    }
                }
            });
    }
}
