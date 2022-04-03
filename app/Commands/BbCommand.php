<?php

namespace App\Commands;

use DB;
use LaravelZero\Framework\Commands\Command;
use Illuminate\Support\Str;
use App\Handlers\SlugTranslateHandler;

class BbCommand extends Command
{
    /**
     * The signature of the command.
     * --p Whether the content should be private.
     *
     * @var string
     */
    protected $signature = 'bb {content?} {--p} {--e} {--c} {--id=} {--t}';

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
            $content = DB::table('wp_comments')->where('comment_ID', $this->option('id'))->value('comment_content');
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
            //查询评论

            $comments = DB::table('wp_comments')->select('comment_ID', 'comment_content', 'comment_date')
                ->where('comment_post_ID', $comment_post_ID)
                ->orderBy('comment_ID', 'DESC')
                ->limit(config('bb.num'))
                ->get()
                ->map(function ($row) {
                    return [
                        $this->transContent($row->comment_content),
                        $row->comment_ID,
                        $row->comment_date,
                    ];
                });

            //dd($comments);
            $headers = ['乱弹', 'ID', '时间'];
            $this->table($headers, $comments); //borderless, 'compact'
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
                DB::update(
                    'UPDATE wp_posts SET comment_count =(SELECT COUNT(*) FROM wp_comments WHERE comment_post_ID = ? AND comment_approved = 1) WHERE ID = ?',
                    [$comment_post_ID, $comment_post_ID]
                );
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
        $trans = app(SlugTranslateHandler::class)->translate($content);
        return $content . PHP_EOL . $trans;
    }
}
