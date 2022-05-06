<p align="center">
    <img title="Laravel Zero" height="100" src="https://raw.githubusercontent.com/laravel-zero/docs/master/images/logo/laravel-zero-readme.png" />
</p>

<p align="center">
  <a href="https://github.com/laravel-zero/framework/actions"><img src="https://img.shields.io/github/workflow/status/laravel-zero/framework/Tests.svg" alt="Build Status"></img></a>
  <a href="https://packagist.org/packages/laravel-zero/framework"><img src="https://img.shields.io/packagist/dt/laravel-zero/framework.svg" alt="Total Downloads"></a>
  <a href="https://packagist.org/packages/laravel-zero/framework"><img src="https://img.shields.io/packagist/v/laravel-zero/framework.svg?label=stable" alt="Latest Stable Version"></a>
  <a href="https://packagist.org/packages/laravel-zero/framework"><img src="https://img.shields.io/packagist/l/laravel-zero/framework.svg" alt="License"></a>
</p>

<h4> <center>This is a <bold>community project</bold> and not an official Laravel one </center></h4>

Laravel Zero was created by, and is maintained by [Nuno Maduro](https://github.com/nunomaduro), and is a micro-framework that provides an elegant starting point for your console application. It is an **unofficial** and customized version of Laravel optimized for building command-line applications.

-   Built on top of the [Laravel](https://laravel.com) components.
-   Optional installation of Laravel [Eloquent](https://laravel-zero.com/docs/database/), Laravel [Logging](https://laravel-zero.com/docs/logging/) and many others.
-   Supports interactive [menus](https://laravel-zero.com/docs/build-interactive-menus/) and [desktop notifications](https://laravel-zero.com/docs/send-desktop-notifications/) on Linux, Windows & MacOS.
-   Ships with a [Scheduler](https://laravel-zero.com/docs/task-scheduling/) and a [Standalone Compiler](https://laravel-zero.com/docs/build-a-standalone-application/).
-   Integration with [Collision](https://github.com/nunomaduro/collision) - Beautiful error reporting

---

# zero_tools

零工(一个用 PHP 写的命令行工具合集)

### 当前功能

1. **force** “原力与你同在” 命令随机展示一条星球大战电影里的台词。

---

2. **bb** “发布 wordpress 站点评论” 需要配置对应 wp 站点的数据连接和待评论文章信息，作者信息。（demo: 🔗[https://blog.phpf5.com/bb](https://blog.phpf5.com/bb) ）

    - 不带参数 （显示最新的 10 条评论）
    - 引号包裹的字符串 （作为评论内容）
    - --q[=关键字] 关键字搜索
    - --e 编辑模式（修改最后一条评论）
    - --et 最后一条评论附加翻译（此时无需内容参数）
    - --c 计数（返回对应文章一共有多少条评论）
    - --id[=ID] 返回特定 ID 的评论内容，--id=-1 输出最后一条评论内容
    - --t 内容调用百度翻译, 换行附加在原本内容后面

3. **mo** "假装（就是）在摸鱼"

---

## Documentation

For full documentation, visit [laravel-zero.com](https://laravel-zero.com/).

## Support the development

**Do you like this project? Support it by donating**

-   PayPal: [Donate](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=66BYDWAT92N6L)
-   Patreon: [Donate](https://www.patreon.com/nunomaduro)

## License

Laravel Zero is an open-source software licensed under the MIT license.
