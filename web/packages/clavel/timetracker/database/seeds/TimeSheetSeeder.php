<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TimeSheetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('timesheet')->delete();

        factory(Clavel\TimeTracker\Models\TimeSheet::class, 100)->create();
    }
}
