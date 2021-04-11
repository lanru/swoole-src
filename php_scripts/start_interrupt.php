<?php
//开启短名
// Co::start_interrupt();
//不开启短名
Swoole\Coroutine::start_interrupt();
for (;;) {
    echo "1\n";
    sleep(1);
}