<?php
require __DIR__.'/vendor/autoload.php';

$server = new \Swoole\Server('127.0.0.1', 9010,
    SWOOLE_BASE);
$server->set([
    'worker_num' => 1
]);
$server->on('start', function (Swoole\Server $server) {
    echo "start\n";
    Swoole\Timer::tick(1000, function () {
        echo "timer tick\n";
    });
});
$server->on('receive', function () { });
$server->start();