<?php

return [
    // 订阅列表必须有名字
    'subscribe_name' => 'my subscribe',
    'ssr'            => [
        // 'ssr://xxx',
    ],
    'ss'             => [
        // ss是可以升级至ssr的，如果你有ss也可以添加进来
        // 'ss://xxx',
    ],
    'access_token'   => [
        // 键只是给自己一个备注而已
        // 以免当分配了过多access_token时不知道哪个打哪个
        // 想注销access_token？直接删了好了
        '自己人'  => 'abc',
        'XX群友' => 'def',
        'YY群友' => 'ghi',
    ],
];
