<?php
$server = new Swoole\Websocket\Server("127.0.0.1", 9501,
    SWOOLE_BASE);
$fds = [];
$server->on('open', function($server, $req) use (&$fds) {
    echo "connection open: {$req->fd}\n";
    $fds[] = $req->fd;
});
$server->on('message', function($server, $frame) use
(&$fds) {
    echo "received message: {$frame->data}\n";
    foreach ($fds as $fd) {
        $server->push($fd, $frame->data);
    }
});
$server->on('close', function($server, $fd) use (&$fds) {
    echo "connection close: {$fd}\n";
    if (($key = array_search($fd, $fds)) !== false) {
        unset($fds[$key]);
    }
});
$server->start();