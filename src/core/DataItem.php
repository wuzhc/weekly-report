<?php
/**
 * Created by PhpStorm.
 * User: wuzhc
 * Date: 19-4-15
 * Time: 下午1:36
 */

namespace gitter\core;

class DataItem
{
    public $author;
    public $message;
    public $date;

    public function __toString()
    {
        $w = date('w', strtotime($this->date));
        return '[ ' . $this->date . '(星期' . $w . ') ' . $this->author . ' ] ' . $this->message . PHP_EOL;
    }
}