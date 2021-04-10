<?php
declare(strict_types=1);

use Swoole\Database\PDOConfig;
use Swoole\Database\PDOPool;
use Swoole\Runtime;

const N = 5;
Runtime::enableCoroutine();
$s = microtime(true);
Co\run(function () {
    $pool = new PDOPool(
        (new PDOConfig())->withHost('106.52.105.89')
            ->withPort(33066)
            ->withDbName('news_db')
            ->withCharset('utf8mb4')
            ->withUsername('erp_shop')
            ->withPassword('xy@A8dMiY')
    );
    for ($n = N; $n--;) {
        go(function () use ($pool) {
            $pdo = $pool->get();
            $statement = $pdo->prepare('SELECT ? + ?');
            if (!$statement) {
                throw new RuntimeException('Prepare 
failed');
            }
            $a = mt_rand(1, 100);
            $b = mt_rand(1, 100);
            $result = $statement->execute([$a, $b]);
            if (!$result) {
                throw new RuntimeException('Execute 
failed');
            }
            $result = $statement->fetchAll();
            if ($a + $b !== (int)$result[0][0]) {
                throw new RuntimeException('Bad result');
            }
            $pool->put($pdo);
        });
    }
});