<?php
const N = 10;
$tasks = range(0, 12);
$workers = [];
$i = 0;
while ($i++ < N) {
    $process = new Swoole\Process(function ($worker) {
        while (1) {
            $task = $worker->pop();
            sleep(1);
            echo "$task\n";
        }

    }, FALSE);
    $process->useQueue(0, 2);
    $pid = $process->start();
    $workers[$i] = $process;
}
$j = 1;
foreach ($tasks as $task) {
    $workers[$j]->push($task);
    if ($j++ == N) {
        $j = 1;
    }
}
$i = 0;
while ($i++ < N) {
    Swoole\Process::wait();
}