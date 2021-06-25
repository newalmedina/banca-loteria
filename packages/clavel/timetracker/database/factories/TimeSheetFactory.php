<?php

use App\Models\User;
use Carbon\Carbon;
use Clavel\TimeTracker\Models\Activity;
use Clavel\TimeTracker\Models\TimeSheet;
use Clavel\TimeTracker\Models\Project;
use Faker\Generator as Faker;

$factory->define(TimeSheet::class, function (Faker $faker) {
    $activity = null;
    do {
        $project = Project::all()->random();
        $activity = Activity::where("project_id", $project->id)->first();
    } while (empty($activity));

    $startDate = Carbon::now()->subDays(rand(1, 20));
    $endDate = $startDate->copy()->addHours(rand(1, 5))->addMinutes(rand(1, 45));
    $duration = $endDate->diffInSeconds($startDate);
    return [
        'description' => $faker->paragraph(5),
        'user_id' => User::all()->random()->id,
        'project_id' => $project->id,
        'activity_id' => $activity->id,
        'start_time' => $startDate,
        'end_time' => $endDate,
        'duration' => $duration,
        'timezone' => 'Europe/Madrid',
        'exported' => $faker->boolean(),
        'rate' => $faker->randomFloat(2, 100, 30000),
        'fixed_rate' => $faker->randomFloat(2, 100, 30000),
        'hourly_rate' => $faker->randomFloat(2, 10, 160)
    ];
});
