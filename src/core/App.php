<?php
/**
 * Created by PhpStorm.
 * User: wuzhc
 * Date: 19-4-16
 * Time: 上午8:42
 */

namespace gitter\core;


use Dotenv\Dotenv;
use Exception;
use gitter\output\ConsoleOutput;
use gitter\output\ExcelOutput;
use gitter\output\TextOutput;

class App
{
    public function __construct()
    {
        if (php_sapi_name() !== 'cli') {
            Response::error('cli模式下允许');
        }

        $this->init();
    }

    public function init()
    {
        $dotenv = Dotenv::create(dirname(dirname(__DIR__)));
        $dotenv->load();
        $this->registerExceptionHandler();
    }

    public function registerExceptionHandler()
    {
        set_exception_handler(function (Exception $exception) {
            Response::error($exception->getMessage());
        });
    }

    public function run()
    {
        global $argv;
        $method = $argv[1];
        if (!$method) {
            $this->help();
        }

        list($opt, $val) = explode('=', $method, 2);
        if ($opt != '-m') {
            $this->help();
        }
        if (!in_array($val, ['excel', 'text', 'console'])) {
            $this->help();
        }

        $data = (new Parser())->exec();
        switch ($val) {
            case 'excel':
                (new ExcelOutput($data))->export();
                break;
            case 'text':
                (new TextOutput($data))->export();
                break;
            case 'console':
                (new ConsoleOutput($data))->export();
                break;
            default:
                $this->help();
        }
    }

    protected function help()
    {
        echo 'Usage: ' . PHP_EOL;
        echo '      php index.php -m=excel|text|console';
        echo PHP_EOL;
        exit;
    }
}