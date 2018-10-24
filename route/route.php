<?php

use nulastudio\Middleware;
use nulastudio\SSR\SSR;
use nulastudio\SSR\SSRGroup;

$access_tokens = [
    '自己人'  => 'abc',
    'XX群友' => 'def',
    'YY群友' => 'ghi',
];
$SSRs = require ROOT_DIR . '/ssr.php';
$SSs  = [];
if (file_exists(ROOT_DIR . '/ss.php')) {
    $SSs = require ROOT_DIR . '/ss.php';
}

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

Router::get('subscribe', middleware(function (...$params) {
    global $SSRs;
    global $SSs;
    $SSRGroup       = new SSRGroup();
    $SSRGroup->name = 'LASSRs';

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
    // foreach ($SSs as $ss_str) {
    //     var_dump($ss_str);
    //     var_dump(SS::SSFromLink($ss_str));
    //     // $SSRGroup->addSSR(SSR::SSRFromSS(SS::SSFromLink($ss_str)));
    // }
    return (string) $SSRGroup;
}));
Router::get('dumpgg', function (...$params) {
    global $SSRs;
    global $SSs;
    @header('Content-Type: text/html; charset=utf-8');
    foreach (array_merge($SSRs, $SSs) as $ssr_str) {
        // echo '<pre>';
        // var_dump(SSR::SSRFromLink($ssr_str));
        $ssr = SSR::SSRFromLink($ssr_str);
        echo "{$ssr->host}:{$ssr->port}";
        if ($ssr->remarks) {
            echo "[{$ssr->remarks}]";
        }
        echo '<hr />';
        // echo Util::urlsafe_b64decode(trim($ssr_str,'ssr://')) . "<br /><hr />";
    }
});
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
