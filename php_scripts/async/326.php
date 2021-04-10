<?php

use Swoole\Constant;
use Swoole\Coroutine\FastCGI\Proxy;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\Http\Server;

$documentRoot = '/Users/wlh/Documents/www';
$server = new Server('0.0.0.0', 8080, SWOOLE_BASE);
$server->set([
    Constant::OPTION_WORKER_NUM               => swoole_cpu_num() * 2,
    Constant::OPTION_HTTP_PARSE_COOKIE        => false,
    Constant::OPTION_HTTP_PARSE_POST          => false,
    Constant::OPTION_DOCUMENT_ROOT            => $documentRoot,
    Constant::OPTION_ENABLE_STATIC_HANDLER    => true,
    Constant::OPTION_STATIC_HANDLER_LOCATIONS =>
        ['/wp-admin', '/wp-content', '/wp-includes'],
]);
$proxy = new Proxy('127.0.0.1:9000', $documentRoot);
$server->on('request', function (Request $request, Response $response) use ($proxy) {
    $proxy->pass($request, $response);
});
$server->start();