<?php

$handle1 = fopen("/Users/wlh/Documents/study/c/learnc/swoole/swoole-src/php_scripts/file.txt", "r");
$handle2 = fopen("/Users/wlh/Documents/study/c/learnc/swoole/swoole-src/php_scripts/file.txt", "r");

$content = fread($handle1, 3);

echo "The process reads three bytes through handle1, the content is: " . $content . PHP_EOL;

$content1 = fread($handle1, 1);
$content2 = fread($handle2, 1);

echo "The process reads one bytes through handle1, the content is: " . $content1 . PHP_EOL;
echo "The process reads one bytes through handle2, the content is: " . $content2 . PHP_EOL;