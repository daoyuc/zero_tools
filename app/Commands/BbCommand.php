<?php

namespace App\Commands;

use DB;
use LaravelZero\Framework\Commands\Command;
use Illuminate\Support\Str;
use App\Handlers\SlugTranslateHandler;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

Carbon::setLocale(config('app.locale'));

class BbCommand extends Command
{
    /**
     * The signature of the command.
     * --p Whether the content should be private.
     *
     * @var string
     */
    protected $signature = 'bb {content?} {--p} {--e} {--c} {--id=} {--t} {--q=} {--f} {--et}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Display recent phpf5 bb';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $content = $this->argument('content');

        $p = $this->option('p');
        //dd($content, $p);
        //^ null
        //^ false
        $comment_post_ID = config('bb.post_id'); //定义你的说说页面ID

        if (!$comment_post_ID) {
            $this->error('config bb.post_id is null!');
            exit;
        }

        if ($this->option('id')) {
            if ($this->option('id') == -1) {
                $content = DB::table('wp_comments')
                    ->where('comment_post_ID', $comment_post_ID)
                    ->orderBy('comment_ID', 'desc')->first();
                $content = $content->comment_content ?: '';
            } else {
                $content = DB::table('wp_comments')->where('comment_ID', $this->option('id'))->value('comment_content');
            }
            if ($content)
                $this->line($content);
            else
                $this->error('Null');
            exit;
        }

        if ($this->option('c')) {
            $count = DB::table('wp_posts')->where('ID', $comment_post_ID)->value('comment_count');
            $this->info('当前一共有' . $count . '条说说');
            exit;
        }
        $comment_author = config('bb.author');
        $user_id = config('bb.user_id');
        $comment_author_email = config('bb.email');

        if (!$content) {

            //修改上一条，附加翻译
            if ($this->option('et')) {
                $content = DB::table('wp_comments')
                    ->where('comment_post_ID', $comment_post_ID)
                    ->orderBy('comment_ID', 'desc')->first();
                $content = $content->comment_content ?: '';

                $content = $this->translateContent($content);

                //修改上一条
                $affected = DB::update(
                    'UPDATE wp_comments SET comment_content = ? WHERE comment_post_ID = ? AND comment_approved = 1 ORDER BY comment_id DESC LIMIT 1',
                    [$content, $comment_post_ID]
                );
                if ($affected) {
                    $this->info('edit and trans done');
                    $this->line($content);
                } else {
                    $this->error('edit and trans ooops!');
                }
                exit;
            }

            //查询评论
            $where = [['comment_post_ID', $comment_post_ID]];
            $title = '乱弹';
            if ($this->option('q')) {
                $where[] = ['comment_content', 'like', '%' . $this->option('q') . '%'];
                $title = '包含"' . $this->option('q') . '"的' . $title;
            }

            $comments = DB::table('wp_comments')->select('comment_ID', 'comment_content', 'comment_date')
                ->where($where)
                ->orderBy('comment_ID', 'DESC')
                ->limit(config('bb.num'))
                ->get()
                ->map(function ($row) {
                    return [
                        $this->transContent($row->comment_content),
                        $row->comment_ID,
                        Carbon::createFromTimeString($row->comment_date)->diffForHumans(),
                    ];
                });

            $style = config('bb.style');
            if ($style == 'table') {

                //行间插入空行
                $showComments = [];
                foreach ($comments as $key => $item) {
                    $showComments[] = $item;
                    $showComments[] = ['', '', '',];
                }

                $headers = [$title, 'ID', '时间'];
                $this->table($headers, $showComments); //borderless, 'compact'
            } else {
                foreach ($comments as $value) {
                    $this->line('<info>' . $value[1] . '</info>  ' . $value[0] . ' <question>(' . $value[2] . ')</question>');
                    $this->line('');
                }
            }
        } else {
            if ($this->option('e')) {
                if ($this->option('t')) {
                    $content = $this->translateContent($content);
                }
                //修改上一条
                $affected = DB::update(
                    'UPDATE wp_comments SET comment_content = ? WHERE comment_post_ID = ? AND comment_approved = 1 ORDER BY comment_id DESC LIMIT 1',
                    [$content, $comment_post_ID]
                );
                if ($affected) {
                    $this->info('edit done');
                    $this->line($content);
                } else {
                    $this->error('edit ooops!');
                }
                exit;
            }

            if ($this->option('t')) {
                $content = $this->translateContent($content);
            }
            $inSql = 'INSERT INTO wp_comments
            (comment_post_ID,
            comment_author,
            comment_author_email,
            comment_date,
            comment_date_gmt,
            comment_content,
            comment_agent,
            `user_id`)
            VALUES (?,?,?,?,?,?,?,?)';
            $newId = DB::insert($inSql, [
                $comment_post_ID,
                $comment_author,
                $comment_author_email,
                gmdate('Y-m-d H:i:s', time() + 8 * 3600),
                date('Y-m-d H:i:s'),
                $content,
                'cmd',
                $user_id,
            ]);
            if ($newId) {
                $this->info('done');
                $this->line($content);
                DB::update(
                    'UPDATE wp_posts SET comment_count =(SELECT COUNT(*) FROM wp_comments WHERE comment_post_ID = ? AND comment_approved = 1) WHERE ID = ?',
                    [$comment_post_ID, $comment_post_ID]
                );

                // 判断是否需要同步到别的平台
                if ($this->option('f') && config('bb.flomo')) {
                    $response = Http::withoutVerifying()->post(config('bb.flomo'), [
                        'content' => '#bb ' . $content,
                    ]);
                    if ($response->successful()) {
                        $this->info('flomo ✔');
                    } else {
                        $this->error('flomo ❌, please check your FLOMO env');
                    }
                }
            } else {
                $this->error('ooops!');
            }
        }

        //$this->info('Simplicity is the ultimate sophistication.');
    }

    private function transContent($content)
    {
        $str = str_replace(PHP_EOL, '↙ ', $content); //换行符替换为符号 ↙
        return Str::limit($str, config('bb.length'));
    }

    private function translateContent($content)
    {
        //判断字符串语言组成
        if (stringType($content) == '1') {
            $trans = app(SlugTranslateHandler::class)->translate($content, 'en', 'zh');
        } else {
            $trans = app(SlugTranslateHandler::class)->translate($content);
        }
        return $content . PHP_EOL . $trans;
    }
}
