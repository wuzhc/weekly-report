<?php
/**
 * Created by PhpStorm.
 * User: wuzhc
 * Date: 19-4-15
 * Time: 下午1:18
 */

namespace gitter\output;

use gitter\core\DataItem;
use gitter\core\Response;

class TextOutput extends Output
{
    /**
     * 导出
     */
    public function export()
    {
        $this->target = $this->target ?: getenv('USERNAME') . '_' . date('W', time()) . '周' . '_工作报告.txt';
        $fp = fopen($this->target, 'a+');
        if (!$fp) {
            Response::error('目标文件生成失败');
        }

        echo '正在导出中...' . PHP_EOL;

        $msg = '--------------------------------------------------------------' . PHP_EOL;
        $msg .= '--------------------- ' . $this->source[0]->author . '的报告 ----------------------------' . PHP_EOL;
        $msg .= '--------------------------------------------------------------' . PHP_EOL;
        fwrite($fp, $msg);

        if (!$this->source) {
            fwrite($fp, '没有报告内容' . PHP_EOL);
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
            fwrite($fp, '>> ' . $date . PHP_EOL);
            foreach ($messages as $message) {
                fwrite($fp, $message . PHP_EOL);
            }
            fwrite($fp, PHP_EOL);
        }

        fclose($fp);
        echo '导出完毕,文件保存在' . dirname(dirname(__DIR__)) . '/' . $this->target . PHP_EOL;
    }
}