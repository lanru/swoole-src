<?php
$server = new Swoole\HTTP\Server("127.0.0.1", 9501);
//你也可以在服务器 WorkerStart 回调函数内处理，那么你的调度器的生命周期和服务器的生命周期是一样的。你不需要对调度器的启动或停止进行任何其他的处理。
$server->on("workerStart", function () {
    
    $id = Swoole\Timer::tick(100, function () {
        echo "Do something...\n";
    });
});
$server->on("start", function (Swoole\Http\Server $server) {
    echo "Swoole http server is started at http://127.0.0.1:9501\n";
});
$server->on("request", function (Swoole\Http\Request $request, Swoole\Http\Response $response) {
    $response->header("Content-Type", "text/plain");
    $response->end("Hello World\n");
});
$server->start();