<?php
$chan = new Swoole\Coroutine\Channel(1);
Co\run(function () use ($chan) {
    $cid = Swoole\Coroutine::getuid();
    $i = 0;
    while (1) {
        co::sleep(1.0);
        $chan->push(['rand' => rand(1000, 9999), 'index'
                            => $i]);
        echo "[coroutine $cid] - $i\n";
        $i++;
    }
});
Co\run(function () use ($chan) {
    $cid = Swoole\Coroutine::getuid();
    while(1) {
        $data = $chan->pop();
        echo "[coroutine $cid]\n";
        var_dump($data);
    }
});