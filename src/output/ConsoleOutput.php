<?php
/**
 * Created by PhpStorm.
 * User: wuzhc
 * Date: 19-4-15
 * Time: 下午1:47
 */

namespace gitter\output;


use gitter\core\DataItem;
use gitter\core\Response;

class ConsoleOutput extends Output
{
    public function export()
    {
        echo '---------------------------------------------------------------' . PHP_EOL;
        echo '--------------------- ' . $this->source[0]->author . '的报告 ----------------------------' . PHP_EOL;
        echo '---------------------------------------------------------------' . PHP_EOL;

        if (!$this->source) {
            echo '没有报告内容' . PHP_EOL;
            exit;
        }

        $data = [];

        /** @var DataItem $item */
        foreach ($this->source as $item) {
            if (!isset($data[$item->date])) {
                $data[$item->date] = [];
            }

            $data[$item->date][] = $item->message;
        }

        ksort($data);

        foreach ($data as $date => $messages) {
            echo '>> ' . $date . PHP_EOL;
            foreach ($messages as $message) {
                echo $message . PHP_EOL;
            }
            echo PHP_EOL;
        }
    }
}