<?php

class RedisPool
{
    protected $available = true;
    protected $pool;
    protected $N = 5;

    public function __construct()
    {
        $this->pool = new SplQueue;
        while ($this->N-- > 0) {
            $redis = new Swoole\Coroutine\Redis();
            $client = $redis->connect('127.0.0.1',
                6379);
            $this->pool->push($client);
        }
    }

    public function put($redis)
    {
        $this->pool->push($redis);
    }

    public function get()
    {
        if ($this->available && count($this->pool) > 0) {
            return $this->pool->pop();
        }
        sleep(0.01);
        return $this->get();
    }

    public function destruct()
    {
        $this->available = false;
        while (!$this->pool->isEmpty()) {
            $this->pool->pop();
        }
    }
}

use Swoole\Http\Server;
use Swoole\Http\Request;
use Swoole\Http\Response;

$pool = new RedisPool();
$server = new Server('127.0.0.1', 9501);
$server->on('workerExit', function (Server $server) use ($pool) {
    $pool->destruct();
});
$server->on('request', function (Request $req, Response $resp) use ($pool) {
    $redis = $pool->get();
    if ($redis === false) {
        $resp->end("ERROR");
        return;
    }
    $result = $redis->hgetall('key');
    $resp->end(var_export($result, true));
    $pool->put($redis);
});
$server->start();