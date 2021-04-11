<?php

$config = [
    'reactor_num' =>1, // Reactor线程个数
    'worker_num' => 1, // Worker进程个数
];

$serv = new swoole_server("0.0.0.0", 9501, SWOOLE_PROCESS, SWOOLE_SOCK_TCP);

$serv->set($config);

$serv->on('connect', function ($serv, $fd) {
    echo "Client: Connect.\n";
});

$serv->on('receive', function ($serv, $fd, $from_id, $data) {
    $serv->send($fd, "Server: ".$data);
});
$serv->on('close', function ($serv, $fd) {
    echo "Client: Close.\n";
});

$serv->start();