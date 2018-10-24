<?php

use nulastudio\Middleware;
use nulastudio\SSR\SSR;
use nulastudio\SSR\SSRGroup;

$config        = require CONFIG_DIR . '/config.php';
$access_tokens = $config['access_token'];
$SSRs          = require ROOT_DIR . '/ssr.php';
$SSs           = require ROOT_DIR . '/ss.php';
$SSRs          = array_merge($SSRs, $config['ssr']);
$SSs           = array_merge($SSs, $config['ss']);

$middlewares = [
    function ($next, ...$params) {
        $usage_data;
        function readUsageData(&$usage_data)
    {
            $usage_data = @json_decode(file_get_contents(DATA_DIR . '/usage.json'), true);
            $usage_data = $usage_data ?: ['open' => 0, 'subscribe' => 0];
        }
        function saveUsageData($usage_data)
    {
            file_put_contents(DATA_DIR . '/usage.json', json_encode($usage_data));
        }

        readUsageData($usage_data);
        $usage_data['open']++;

        $return = $next(...$params);

        if ($return) {
            $usage_data['subscribe']++;
        }
        saveUsageData($usage_data);

        return $return;
    },
    function ($next, ...$params) use ($access_tokens) {
        $access_token = isset($_GET['ACCESS_TOKEN']) ? $_GET['ACCESS_TOKEN'] : '';
        $access_token = preg_replace('/\W/', '', $access_token);
        if (empty($access_token)) {
            $access_token = 'fuckoffman';
        }
        if (!in_array($access_token, $access_tokens, true)) {
            return;
        }
        return $next(...$params);
    },
];

Router::get('subscribe', middleware(function (...$params) use ($SSRs, $SSs, $config) {
    $SSRGroup       = new SSRGroup();
    $SSRGroup->name = $config['subscribe_name'];

    // subscribe list can not be empty!
    if (empty($SSRs) && empty($SSs)) {
        $SSRGroup->addSSR(SSR::SSRFromArray([
            'host'     => '127.0.0.1',
            'port'     => '5566',
            'method'   => 'none',
            'password' => 'none',
            'protocol' => 'origin',
            'obfs'     => 'plain',
            'remarks'  => 'DO NOT CONNECT',
        ]));
    }

    foreach (array_merge($SSRs, $SSs) as $ssr_str) {
        $SSRGroup->addSSR(SSR::SSRFromLink($ssr_str));
    }
    return (string) $SSRGroup;
}));
Router::get('usage', function (...$params) {
    @header('Content-Type: application/json; charset=utf-8');
    echo @file_get_contents(DATA_DIR . '/usage.json');
});

// 404处理
// Router::error(function () {echo 'opps';});

// 模板渲染
Router::dispatch();

function middleware(callable $callback)
{
    global $middlewares;
    return (new Middleware)->send()->to($callback)->through($middlewares)->finish(function ($origin, $data) {
        // 响应封装器
        if (is_array($data)) {
            @header('Content-Type: application/json; charset=utf-8');
            echo json_encode($data);
        } else if (is_string($data)) {
            echo $data;
        }
    })->pack();
}

function jsonData($err_no = 0, $err_msg = '', $data = null)
{
    return [
        'err_no'        => (int) $err_no,
        'err_msg'       => (string) $err_msg,
        'data'          => is_array($data) ? $data : null,
        'response_time' => microtime(true),
    ];
}
