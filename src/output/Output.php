<?php
/**
 * Created by PhpStorm.
 * User: wuzhc
 * Date: 19-4-15
 * Time: 下午1:19
 */

namespace gitter\output;

abstract class Output
{
    protected $source = [];
    protected $target = '';
    protected $template = '';

    abstract public function export();

    public function __construct($data)
    {
        $this->source = $data;
    }
}