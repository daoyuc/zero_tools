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
        $z = date('z');
        $this->info('今天是 ' . Carbon::now()->toDateString() . ' 今年的第 ' . $z . ' 天');
        $percent    = ceil($z / 365 * 100);
        $dark_num   = $percent / 5;
        $dark_num   = $dark_num <= 20 ? $dark_num : 20;
        $bright_num = 20 - $dark_num;
        $bright_num = $bright_num >= 0 ? $bright_num : 0;
        $this->info('🌏已经公转 ' . str_repeat('▓', $dark_num) . str_repeat('░', $bright_num) . ' ' . $percent . '%');
        $this->line('正在摸🐟，别催了别催了~');
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
