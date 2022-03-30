<?php

namespace App\Commands;

use DB;
use LaravelZero\Framework\Commands\Command;

class BbCommand extends Command
{
    /**
     * The signature of the command.
     * --p Whether the content should be private.
     *
     * @var string
     */
    protected $signature = 'bb {content?} {--p}';

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
        $comment_post_ID = 109; //定义你的说说页面ID
        $comment_author = 'pwa';
        $user_id = 1;

        if (!$content) {
            //查询评论

            $comments = DB::table('wp_comments')->select('comment_content', 'comment_date')
            ->where('comment_post_ID', $comment_post_ID)->orderBy('comment_ID', 'DESC')->get(5)
            ->map(function ($row) {
                return [
                    $this->transContent($row->comment_content), $row->comment_date,
                ];
            });

            //dd($comments);
            $headers = ['乱弹', '时间'];
            $this->table($headers, $comments); //borderless, 'compact'
        } else {
            $inSql = 'INSERT INTO wp_comments
            (comment_post_ID, comment_author,comment_date, comment_date_gmt,comment_content,comment_agent,`user_id`)
            VALUES (?,?,?,?,?,?,?)';
            $newId = DB::insert($inSql, [
                $comment_post_ID, $comment_author, gmdate('Y-m-d H:i:s', time() + 8 * 3600), date('Y-m-d H:i:s'), $content, 'cmd', $user_id,
            ]);
            if ($newId) {
                $this->info('done');
            } else {
                $this->error('ooops!');
            }
        }

        //$this->info('Simplicity is the ultimate sophistication.');
    }

    private function transContent($content)
    {
        return str_replace(PHP_EOL, '↙ ', $content); //换行符替换为浮号 ↙
    }
}
