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
        $this->checkEnv();
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

        $app = null;
        $data = (new Parser())->exec();
        switch ($val) {
            case 'excel':
                $app = new ExcelOutput($data);
                break;
            case 'text':
                $app = new TextOutput($data);
                break;
            case 'console':
                $app = new ConsoleOutput($data);
                break;
            default:
                $this->help();
        }

        $app->export();
    }

    protected function help()
    {
        echo 'Usage: ' . PHP_EOL;
        echo '      php index.php -m=excel|text|console';
        echo PHP_EOL;
        exit;
    }

    protected function checkEnv()
    {
        $author = getenv('AUTHOR');
        if (!$author) {
            Response::error('AUTHOR未定义');
        }

        $startDate = getenv('SINCE_DAY');
        if (!$startDate) {
            Response::error('SINCE_DAY未定义');
        } else {
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $startDate)) {
                Response::error('SINCE_DAY格式错误,你的格式为' . $startDate . ',正确格式为YYYY-mm-dd');
            }
        }

        $endDate = getenv('UNTIL_DAY');
        if (!$endDate) {
            Response::error('UNTIL_DAY未定义');
        } else {
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $endDate)) {
                Response::error('UNTIL_DAY格式错误,你的格式为' . $endDate . ',正确格式为YYYY-mm-dd');
            }
        }

        $since = (strtotime($endDate) - strtotime($startDate)) / (24 * 3600);
        if ($since <= 0) {
            Response::error('开始时间和结束时间范围错误');
        }

        $repositories = getenv('REPOSITORIES');
        if (!$repositories) {
            Response::error('REPOSITORIES未定义');
        }

        $username = getenv('USERNAME');
        if (!$username) {
            Response::error('USERNAME未定义');
        }

        $saveDir = getenv('SAVE_DIR');
        if (!$saveDir) {
            Response::error('SAVE_DIR未定义');
        } else {
            if (!is_dir($saveDir)) {
                Response::error($saveDir . '目录不存在');
            }
        }
    }
}