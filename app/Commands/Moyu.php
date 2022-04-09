<?php

namespace App\Commands;

use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class Moyu extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'mo';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = '每日摸鱼🐟播报';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('今天是' . Carbon::now()->toDateString());

        $this->line('正在摸了，别催了别催了~');
    }

    /**
     * Define the command's schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
