<?php

if (!function_exists('d')) {
    /**
     * 打印数据 print
     *
     * @param $args
     */
    function d(...$args)
    {
        echo '<pre>';
        foreach ($args as $k => $arg) {
            echo '<fieldset><legend>' . ($k + 1) . '</legend>';
            var_dump($arg);
            echo '</fieldset>';
        }
        exit;
    }
}

if (!function_exists('p')) {
    /**
     * 打印数据
     *
     * @param array ...$args
     */
    function p(...$args)
    {
        foreach ($args as $k => $arg) {
            print_r($arg);
            echo PHP_EOL;
        }
        exit;
    }
}