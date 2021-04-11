<?php
Swoole\Coroutine::start_interrupt();
for (;;) {
    echo "1\n";
    sleep(1);
}