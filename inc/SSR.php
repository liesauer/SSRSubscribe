<?php

namespace nulastudio\SSR;

use nulastudio\SSR\SS;
use nulastudio\SSR\Util;

class SSR
{
    const SSR_PREFIX = 'ssr://';
                        // ssr://host:port:protocol:method:obfs:base64pass/?obfsparam=base64&protoparam=base64&remarks=base64&group=base64&udpport=0&uot=1
    public $host;       // 服务器
    public $port;       // 端口
    public $protocol;   // 协议
    public $method;     // 加密方法
    public $obfs;       // 混淆
    public $password;   // 密码
    public $obfsParam;  // 混淆参数
    public $protoParam; // 协议参数
    public $remarks;    // 备注
    public $group;      // 组
    public $uot;        // udp over tcp
    public $udpPort;    // udp端口

    private function __construct()
    {
    }

    public static function SSRFromLink($link)
    {
        if (substr($link, 0, 6) !== self::SSR_PREFIX) {
            return null;
        }
        $ssr_info            = Util::urlsafe_b64decode(substr($link, 6));
        @list($info, $param) = explode('/?', $ssr_info);

        @list($host, $port, $protocol, $method, $obfs, $base64pass) = explode(':', $info);

        $protocol = str_replace('_compatible', '', $protocol);
        $obfs     = str_replace('_compatible', '', $obfs);

        $arr             = [];
        $arr['host']     = $host;
        $arr['port']     = $port;
        $arr['protocol'] = $protocol ?: 'origin';
        $arr['method']   = $method;
        $arr['obfs']     = $obfs ?: 'plain';
        $arr['password'] = Util::urlsafe_b64decode($base64pass);
        foreach (explode('&', $param) as $kv) {
            @list($key, $value) = explode('=', $kv);
            $arr[$key]          = Util::urlsafe_b64decode($value);
        }

        return self::SSRFromArray($arr);
    }

    public static function SSRFromSSLink($link)
    {
        $ss = SS::SSFromLink($link);
        return self::SSRFromSS($ss);
    }

    public static function SSRFromSS(SS $ss)
    {
        return self::SSRFromArray([
            'host'     => $ss->host,
            'port'     => $ss->port,
            'method'   => $ss->method,
            'password' => $ss->password,
            'protocol' => 'origin',
            'obfs'     => 'plain',
        ]);
    }

    public static function SSRFromArray($arr)
    {
        $ssr             = new self();
        $ssr->host       = isset($arr['host']) ? $arr['host'] : '';
        $ssr->port       = isset($arr['port']) ? $arr['port'] : '';
        $ssr->protocol   = isset($arr['protocol']) ? $arr['protocol'] : '';
        $ssr->method     = isset($arr['method']) ? $arr['method'] : '';
        $ssr->obfs       = isset($arr['obfs']) ? $arr['obfs'] : '';
        $ssr->password   = isset($arr['password']) ? $arr['password'] : '';
        $ssr->obfsParam  = isset($arr['obfsparam']) ? $arr['obfsparam'] : '';
        $ssr->protoParam = isset($arr['protoparam']) ? $arr['protoparam'] : '';
        $ssr->remarks    = isset($arr['remarks']) ? $arr['remarks'] : '';
        $ssr->group      = isset($arr['group']) ? $arr['group'] : '';
        $ssr->udpPort    = isset($arr['udpport']) ? $arr['udpport'] : '';
        $ssr->uot        = isset($arr['uot']) ? $arr['uot'] : '';
        return $ssr;
    }

    public function __toString()
    {
        $main_part  = '';
        $base64pass = Util::urlsafe_b64encode($this->password);
        $main_part  = "{$this->host}:{$this->port}:{$this->protocol}:{$this->method}:{$this->obfs}:{$base64pass}";
        $param_str  = 'obfsparam=' . Util::urlsafe_b64encode($this->obfsParam ?: '');
        if (!empty($this->protoParam)) {
            $param_str .= '&protoparam=' . Util::urlsafe_b64encode($this->protoParam);
        }
        if (!empty($this->remarks)) {
            $param_str .= '&remarks=' . Util::urlsafe_b64encode($this->remarks);
        }
        if (!empty($this->group)) {
            $param_str .= '&group=' . Util::urlsafe_b64encode($this->group);
        }
        if ($this->uot) {
            $param_str .= '&uot=1';
        }
        if ($this->udpPort > 0) {
            $param_str .= '&udpport=' . $this->udpPort;
        }
        return self::SSR_PREFIX . Util::urlsafe_b64encode("{$main_part}/?{$param_str}");
    }
}
