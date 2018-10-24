# SSRSubscribe
这是一个用PHP写的小小的酸酸乳订阅。

## 配置
编辑 `config/config.php` ，所有的东西都在里面了
```php
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
```

## 初始化
```shell
cd PROJECT_DIR
composer install
```

## 怎么运行
本地
```shell
cd PROJECT_DIR
php -S localhost:8080
```

服务器
丢到服务器上面就完事了

## 爬虫？
如果你有爬虫程序，可以直接将爬来的酸酸乳或者酸酸链接写到对应的`ssr.php`以及`ss.php`

参考`SSSpider.php`

然后将爬虫脚本添加到云监控上面定时采集就完事了

## 路由
```text
/usage
```
```text
/subscribe?ACCESS_TOKEN=xxx
```

如果你的程序不是放在根目录下，假设放在`ssr`目录下，那么对应的路由就是
```text
/ssr/usage
```
```text
/ssr/subscribe?ACCESS_TOKEN=xxx
```
