<?php
/**
 * Created by PhpStorm.
 * User: wuzhc
 * Date: 19-4-15
 * Time: 下午1:50
 */

namespace gitter\core;


class Response
{
    public static function error($msg)
    {
        echo "\033[32m " . '[ error ] :' . "\033[0m" . ' ' . $msg . PHP_EOL;
        exit;
    }

    public static function success($msg)
    {
        echo "\031[32m " . '[ success ] :' . "\031[0m" . ' ' . $msg . PHP_EOL;
        exit;
    }
}