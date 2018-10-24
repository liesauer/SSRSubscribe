<?php

define('ROOT_DIR', __DIR__);
define('VENDOR_DIR', ROOT_DIR . '/vendor');
define('INC_DIR', ROOT_DIR . '/inc');
define('DATA_DIR', ROOT_DIR . '/data');
define('ROUTE_DIR', ROOT_DIR . '/route');
define('CONFIG_DIR', ROOT_DIR . '/config');

require VENDOR_DIR . '/autoload.php';

error_reporting(~E_ALL);

date_default_timezone_set('Asia/Shanghai');

foreach (glob(INC_DIR . '/*.php') as $file) {
    require_once $file;
}

foreach (glob(ROUTE_DIR . '/*.php') as $route) {
    require_once $route;
}
