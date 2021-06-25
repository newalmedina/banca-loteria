<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        // Procesado de la cola
        // https://laracasts.com/discuss/channels/laravel/queuework-daemon-not-running-with-laravel-scheduler?page=1
        /** Run every minute specified queue if not already started */
        /*
        if (stripos((string) shell_exec('ps xf | grep \'[q]ueue:work\''), 'artisan queue:work') === false) {
            $schedule->command('queue:work --queue=default --sleep=2 --tries=3 --timeout=3600')
                ->everyMinute()
                ->appendOutputTo(storage_path() . '/logs/scheduler.log');
        }
        */

        // Otra opcion
        // https://papertank.com/blog/903/setup-laravel-queue-on-shared-hosting/
        $path = base_path();
        $schedule->call(function () use ($path) {
            $run = true;
            if (file_exists($path . '/queue.pid')) {
                $pid = file_get_contents($path . '/queue.pid');
                $result = exec("ps -p $pid --no-heading | awk '{print $1}'");
                $run = $result == '' ? true : false;
            }

            if ($run) {
                $command = '/usr/bin/php ' . $path .
                    '/artisan queue:work --sleep=2 --tries=3 --timeout=3600 > /dev/null & echo $!';
                $number = exec($command);
                file_put_contents($path . '/queue.pid', $number);
            }
        })->name('monitor_queue_listener')->everyMinute(); //everyFiveMinutes();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
