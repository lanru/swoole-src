<?php
const N = 10;
Co\run(function () {
    $chan = new chan();
    $tasks = range(0, 12);
    go(function () use ($chan, $tasks) {
        foreach ($tasks as $task) {
            $chan->push($task);
        }
    });
    $i = 0;
    while ($i++ < N) {
        go(function () use ($chan) {
            while (1) {
                $task = $chan->pop();
                Co::sleep(1);
                echo "$task\n";
            }
        });
    }
});