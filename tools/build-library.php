#!/usr/bin/env php
<?php
require __DIR__ . '/bootstrap.php';

define('LIBRARY_HEADER', ROOT_DIR . '/ext-src/php_swoole_library.h');
define('PHP_TAG', '<?php');

if (!isset($argv[1]) or $argv[1] != 'dev') {
    preg_match(
        '/^(\d+)/',
        trim(shell_exec('cd ' . LIBRARY_DIR . ' && git diff --shortstat')),
        $file_change
    );
    $file_change = (int) ($file_change[1] ?? 0);
    if ($file_change > 0) {
        swoole_error($file_change . ' file changed in [' . LIBRARY_DIR . ']');
    }
}

$commit_id = trim(shell_exec('cd ' . LIBRARY_DIR . ' && git rev-parse HEAD'));
if (!$commit_id || strlen($commit_id) != 40) {
    swoole_error('Unable to get commit id of library in [' . LIBRARY_DIR . ']');
}

/* Notice: Sort by dependency */
$files = [
    # <basic> #
    'constants.php',
    # <std> #
    'std/exec.php',
    # <core> #
    'core/Constant.php',
    'core/StringObject.php',
    'core/MultibyteStringObject.php',
    'core/Exception/ArrayKeyNotExists.php',
    'core/ArrayObject.php',
    'core/ObjectProxy.php',
    'core/Coroutine/WaitGroup.php',
    'core/Coroutine/Server.php',
    'core/Coroutine/Server/Connection.php',
    'core/Coroutine/Barrier.php',
    'core/Coroutine/Http/functions.php',
    # <core for connection pool> #
    'core/ConnectionPool.php',
    'core/Database/ObjectProxy.php',
    'core/Database/MysqliConfig.php',
    'core/Database/MysqliException.php',
    'core/Database/MysqliPool.php',
    'core/Database/MysqliProxy.php',
    'core/Database/MysqliStatementProxy.php',
    'core/Database/PDOConfig.php',
    'core/Database/PDOPool.php',
    'core/Database/PDOProxy.php',
    'core/Database/PDOStatementProxy.php',
    'core/Database/RedisConfig.php',
    'core/Database/RedisPool.php',
    # <core for HTTP> #
    'core/Http/Status.php',
    # <core for cURL> #
    'core/Curl/Exception.php',
    'core/Curl/Handler.php',
    # <core for FastCGI> #
    'core/FastCGI.php',
    'core/FastCGI/Record.php',
    'core/FastCGI/Record/Params.php',
    'core/FastCGI/Record/AbortRequest.php',
    'core/FastCGI/Record/BeginRequest.php',
    'core/FastCGI/Record/Data.php',
    'core/FastCGI/Record/EndRequest.php',
    'core/FastCGI/Record/GetValues.php',
    'core/FastCGI/Record/GetValuesResult.php',
    'core/FastCGI/Record/Stdin.php',
    'core/FastCGI/Record/Stdout.php',
    'core/FastCGI/Record/Stderr.php',
    'core/FastCGI/Record/UnknownType.php',
    'core/FastCGI/FrameParser.php',
    'core/FastCGI/Message.php',
    'core/FastCGI/Request.php',
    'core/FastCGI/Response.php',
    'core/FastCGI/HttpRequest.php',
    'core/FastCGI/HttpResponse.php',
    'core/Coroutine/FastCGI/Client.php',
    'core/Coroutine/FastCGI/Client/Exception.php',
    'core/Coroutine/FastCGI/Proxy.php',
    # <core for Process> #
    'core/Process/Manager.php',
    # <core for Server> #
    'core/Server/Helper.php',
    # <core for functions> #
    'core/Coroutine/functions.php',
    # <ext> #
    'ext/curl.php',
    'ext/sockets.php',
    # <finalizer> #
    'functions.php',
    'alias.php',
    'alias_ns.php',
];

$diff_files = array_diff(swoole_library_files(), $files);
if (!empty($diff_files)) {
    swoole_error('Some files are not loaded: ', ...$diff_files);
}

foreach ($files as $file) {
    if (!file_exists(LIBRARY_SRC_DIR . '/' . $file)) {
        swoole_error("Unable to find source file [{$file}]");
    }
}

$source_str = $eval_str = '';
foreach ($files as $file) {
    $php_file = LIBRARY_SRC_DIR . '/' . $file;
    if (strpos(`/usr/bin/env php -n -l {$php_file} 2>&1`, 'No syntax errors detected') === false) {
        swoole_error("Syntax error in file [{$php_file}]");
    } else {
        swoole_ok("Syntax correct in [{$file}]");
    }
    $code = file_get_contents($php_file);
    if ($code === false) {
        swoole_error("Can not read file [{$file}]");
    }
    if (strpos($code, PHP_TAG) !== 0) {
        swoole_error("File [{$file}] must start with \"<?php\"");
    }
    $name = unCamelize(str_replace(['/', '.php'], ['_', ''], $file));
    // keep line breaks to align line numbers
    $code = rtrim(substr($code, strlen(PHP_TAG)));
    $code = str_replace(['\\', '"', "\n"], ['\\\\', '\\"', "\\n\"\n\""], $code);
    $code = implode("\n" . space(4), explode("\n", $code));
    $filename = "@swoole-src/library/{$file}";
    $source_str .= "static const char* swoole_library_source_{$name} =\n" . space(4) . "\"{$code}\\n\";\n\n";
    $eval_str .= space(4) . "zend::eval(swoole_library_source_{$name}, \"{$filename}\");\n";
}
$source_str = rtrim($source_str);
$eval_str = rtrim($eval_str);

$generator = basename(__FILE__);
$content = <<<C
/**
 * Generated by {$generator}, Please DO NOT modify!
 */

/* \$Id: {$commit_id} */

{$source_str}

void php_swoole_load_library()
{
{$eval_str}
}

C;

if (file_put_contents(LIBRARY_HEADER, $content) != strlen($content)) {
    swoole_error('Can not write source codes to ' . LIBRARY_HEADER);
}
swoole_success("Generated swoole php library successfully!");
