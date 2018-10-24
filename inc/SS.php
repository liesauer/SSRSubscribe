<?php

namespace nulastudio\SSR;

use nulastudio\SSR\Util;

class SS
{
    const SS_PREFIX = 'ss://';
                      // ss://method:password@server:port
    public $host;     // 服务器
    public $port;     // 端口
    public $method;   // 加密方法
    public $password; // 密码

    private function __construct()
    {
    }

    public static function SSFromLink($link)
    {
        if (substr($link, 0, 5) !== self::SS_PREFIX) {
            return null;
        }
        $ss_info              = Util::urlsafe_b64decode(substr($link, 5));
        @list($info, $server) = explode('@', $ss_info);

        @list($method, $password) = explode(':', $info);
        @list($host, $port)       = explode(':', $server);

        $arr             = [];
        $arr['host']     = $host;
        $arr['port']     = $port;
        $arr['password'] = $password;
        $arr['method']   = $method;

        return self::SSFromArray($arr);
    }

    public static function SSFromArray($arr)
    {
        $ss           = new self();
        $ss->host     = isset($arr['host']) ? $arr['host'] : '';
        $ss->port     = isset($arr['port']) ? $arr['port'] : '';
        $ss->method   = isset($arr['method']) ? $arr['method'] : '';
        $ss->password = isset($arr['password']) ? $arr['password'] : '';
        return $ss;
    }

    public function __toString()
    {
        $str = "{$this->method}:{$this->password}@{$this->host}:{$this->port}";
        return self::SSR_PREFIX . Util::urlsafe_b64encode($str);
    }
}
