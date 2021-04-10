<?php

class HttpProxyServer
{
    protected $server = null;

    function __construct()
    {
        $this->server = new
        Swoole\HTTP\Server('127.0.0.1', 9510, SWOOLE_BASE);
        $this->server->on('request', [
            $this,
            'onRequest'
        ]);
        return $this;
    }

    public function start()
    {
        $this->server->start();
    }

    public function onRequest(swoole_http_request $req,
                              swoole_http_response $resp)
    {
        if ($req->server['request_method'] == 'GET') {
            $client = new
            Swoole\Coroutine\HTTP\Client('127.0.0.1', 3000);
            $client->setHeaders($req->header);
            $client->get($req->server['request_uri']);
            $headers = $client->getHeaders();
            foreach ($headers as $key => $value) {
                $resp->header($key, $value);
            }
            $resp->end($client->body);
        } else {
            $resp->status(405);
            $resp->end("");
        }
    }
}

(new HttpProxyServer())->start();