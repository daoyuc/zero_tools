<?php

return [

    /*
    |--------------------------------------------------------------------------
    | 说说页面ID
    |--------------------------------------------------------------------------
    |
    | 在后台查看文章或页面ID
    |
    */

    'post_id' => env('BB_POST_ID', 0),

    'author' => env('BB_AUTHOR', '匿名'),

    'user_id' => env('BB_USER_ID',  1),

    'email' => env('BB_EMAIL', ''),

    'num' => env('BB_NUM', 10),

    'length' => env('BB_LENGTH', 120), //最多显示几个字, 计算宽度的时候，中文算2个长度，英文算1个长度

    'style' => env('BB_STYLE', 'table'), //内容显示版式， 支持 table 表格 和 line 行间

    'flomo' => env('FLOMO', ''), //flomo记录 API
];
