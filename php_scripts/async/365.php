<?php
$http->on('request', function ($request, $response) {
    $response->header('Content-Type', 'text/event-stream');
    $response->header('Cache-Control', 'no-cache');

    $counter = rand(1, 10);
    while (true) {
        $data = "event: ping\n";
        $response->write($data);
        $curDate = date(DATE_ISO8601);
        $data = 'data: {"time": "' . $curDate . '"}';
        $data .= "\n\n";
        $response->write($data);
        $counter--;
        if (!$counter) {
            $data = 'data: This is a message at time ' .
                $curDate . "\n\n";
            $response->end($data);
            break;
        }
        co::sleep(1);
    }
});