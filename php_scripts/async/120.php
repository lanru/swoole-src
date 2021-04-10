<?php
require __DIR__.'/vendor/autoload.php';

Co\run(function() {
    go(function () {
        try {
            exit();
        } catch (Swoole\ExitException $e) {
            echo $e->getMessage();
        }
    });
});