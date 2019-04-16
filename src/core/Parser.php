<?php
/**
 * Created by PhpStorm.
 * User: wuzhc
 * Date: 19-4-15
 * Time: 下午1:37
 */

namespace gitter\core;

use DateTime;
use Gitter\Client;

class Parser
{
    protected $repositories = [];
    protected $command = '';
    protected $author = '';

    public function __construct()
    {
        $this->repositories = explode(',', getenv('REPOSITORIES'));
        if (!$this->repositories) {
            Response::error('请先定义REPOSITORIES');
        }

        $this->author = getenv('AUTHOR');
        if (!$this->author) {
            Response::error('请先定义AUTHOR');
        }

        $since = getenv('SINCE_DAY');
        if (!$since) {
            Response::error('请先定义SINCE');
        } else {
            $since .=' days ago';
        }

        $this->command = sprintf('--author=%s --since="%s" --all', $this->author, $since);
    }

    public function exec()
    {
        $res = [];

        foreach ($this->repositories as $repository) {
            $temp = $this->getRepositoryCommits($repository);
            $res = array_merge($res, $temp);
        }

        return $res;
    }

    public function getRepositoryCommits($path)
    {
        echo '正在获取' . $path . '仓库commit日志...' . PHP_EOL;

        $client = new Client;
        $repository = $client->getRepository($path);
        $commits = $repository->getCommits($this->command);

        // 结果集
        $res = [];

        // 字符串过滤器
        $filter = [
            // 'test'
        ];

        // 正则过滤器
        $regFilter = [
            '/^Merge branch/', // 去除合并信息
            '/^[a-zA-Z\s]+$/'  // 去除全英文注释,或者加一个英文翻译api,按个人需求
        ];

        /** @var \Gitter\Model\Commit\Commit $commit */
        foreach ($commits as $commit) {
            /** @var DateTime dateObj */
            $date = $commit->getDate()->format('Y-m-d');
            $message = $commit->getMessage();

            if (in_array($message, $filter)) {
                continue;
            } else {
                $filter[] = $message;
            }

            $flag = false;
            foreach ($regFilter as $rf) {
                if (preg_match($rf, $message)) {
                    $flag = true;
                    break;
                }
            }
            if (true === $flag) {
                continue;
            }

            $item = new DataItem();
            $item->author = $this->author;
            $item->message = $message;
            $item->date = $date;
            $res[] = $item;

            echo $item;
        }

        echo PHP_EOL;
        return $res;
    }
}