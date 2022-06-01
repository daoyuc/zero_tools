<?php

namespace App\Commands;

use GuzzleHttp\Cookie\CookieJar;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use LaravelZero\Framework\Commands\Command;

class Storeweb extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'sw {--f : force fetch new data}';

    protected $baseUrl = 'https://api.storeweb.cn/';
    //protected $memberId = 1161; //TODO 修改成你自己的memberId, 不需要自动签到可以设置为0

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'cmds for storeweb.cn';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //$this->info(Http::withOptions(["verify" => false])->get($this->baseUrl));
        //如有有传 force 参数
        //dd($this->option('f'));
        //获取用户ID
        $_memberId = Cache::get('memberId');
        if (!$_memberId) {
            $_memberId = $this->ask('What is your memberId ?');
            if ($_memberId) {
                Cache::put('memberId', $_memberId);
            }
        }
        if ($this->option('f')) {
            $response = false;
        } else {
            $response =  Cache::get('sw_cw');
        }

        //dd();
        if (empty($response)) {
            $response = $this->reqAPI('api/index/signInList');
            if (0 != $response['code']) {
                $this->error('请求失败');
            }
            $this->info($response['msg']);

            /*
        ^ array:3 [
  "code" => 0
  "msg" => "获取签到列表成功"
  "data" => array:20 [
    0 => array:3 [
      "memberId" => 646
      "member" => array:5 [
        "memberId" => 646
        "name" => "JoiT"
        "intro" => "养了一只名叫米崽的 15 斤狸花小猫，天天军训它，所以又名：警猫训练虱"
        "avatarUrl" => "https://upload.storeweb.cn/upload/member/avatar/646/061daaf751db3d2a5dba6c8274d0a272.jpg"
        "sexName" => "男"
      ]
      "createAt" => "2022-05-31 00:03:29"
    ]
    1 => array:3 [
      "memberId" => 1195
      "member" => array:5 [
        "memberId" => 1195
        "name" => "土拨许"
        "intro" => "喂~在吗？在吗？这里是土拨许~"
        "avatarUrl" => "https://upload.storeweb.cn/upload/member/avatar/1195/90e617effb3e0ba2442dc14b745bf384.jpg"
        "sexName" => "男"
      ]
      "createAt" => "2022-05-31 00:17:39"
    ]
        */
            //$this->info($response);
            Cache::put('sw_cw', $response, 3600);
            $this->line('更新缓存...');
        } else {
            $this->line('获取缓存...');
        }
        //行间插入空行
        $columns = [];

        //dd(Arr::pluck($response['data'], 'memberId'));
        $mySignLog = Arr::first($response['data'], function ($value) use ($_memberId) {
            return  $_memberId == $value['memberId'];
        });
        if (empty($mySignLog)) {
            $sessionId = Cache::get('sessionId'); // a string 32 chars
            if (!$sessionId) {
                $sessionId = $this->ask('What is your session-id ?');
            }

            $this->signIn($sessionId);
        } else {
            $this->info('你在' . $mySignLog['createAt'] . '签到了，喵');
        }

        $turn = -1;
        foreach ($response['data'] as $key => $item) {
            $_item = [];
            // Arr::only($item['member'], ['name', 'intro', 'sexName', 'memberId']) + [$item['createAt']];
            $_item['at'] = explode(' ', $item['createAt'])[1];
            $_item['url'] = '<info>https://storeweb.cn/member/o/' . $item['memberId'] . '</info>';
            if ($_memberId == $item['memberId']) {
                $_item['name'] = '⭐' . $item['member']['name'];
                $turn = $key;
            } else {
                $_item['name'] = $item['member']['name'];
            }
            $_item['sex'] = $item['member']['sexName'] == '小猫（保密）' ? '' : $item['member']['sexName'];
            $_item['intro'] = Str::limit($item['member']['intro'], 72);

            $columns[] = $_item;
            //$columns[] = ['', '', '', '', ''];
        }

        $headers = ['⏰ ', '🔗', '😀', '♀♂', 'o(=•ェ•=)m'];
        $this->table($headers, $columns); //borderless, 'compact'
        $this->line('一共有' . count($response['data']) . '个人勤快的签到啦' . (($turn > 0) ? '，你是第' . (++$turn) . '个' : ''));
    }

    private function reqAPI($path = '')
    {
        return Http::withOptions(['verify' => false])->post($this->baseUrl . $path)->throw()->json();
    }

    private function signIn($sessionId = null, $force = false)
    {
        if ($force) {
            $sessionId = $this->ask('[⚠] What is your session-id ?');
        }
        if (!$sessionId) {
            if ($this->confirm('略过签到吗?')) {
                return;
            } else {
                $sessionId = $this->ask('[⚠] What is your session-id ?');
            }
        }

        $signResult = Http::withOptions([
            'verify' => false,
        ])
            ->withHeaders([
                'session-id' => $sessionId,
            ])
            ->post($this->baseUrl . 'api/index/signIn')->throw()->json();
        /*
        //可能的返回值
array:3 [
  "code" => 1999
  "msg" => "登录信息错误，请刷新页面重试"
  "data" => null
]

array:3 [
  "code" => 1
  "msg" => "哟，你今天已经签到了"
  "data" => null
]
        */
        if (1999 != $signResult['code']) {
            if (!Cache::get('sessionId')) {
                Cache::put('sessionId', $sessionId);
            }
            if (1 !== $signResult['code']) {
                Log::info('signIn success');
            }
            $this->info($signResult['msg']);
        } else {
            $this->error($signResult['msg']);
            Log::error('session-id outdated');
            //询问是否更换sessionId
            if ($this->confirm('重新输入 session-id 吗?')) {
                $this->signIn(null, true);
            }
        }
    }

    /**
     * Define the command's schedule.
     */
    public function schedule(Schedule $schedule): void
    {
        $schedule->command(static::class)->dailyAt('00:01'); //每天 00:01 执行一次任务
    }
}
