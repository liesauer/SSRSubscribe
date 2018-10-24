<?php

namespace nulastudio\SSR;

use nulastudio\SSR\SSR;
use nulastudio\SSR\Util;

class SSRGroup
{
    protected $SSRs = [];
    public $name    = 'empty group';

    public function __construct($ssr = null)
    {
        if (is_array($ssr)) {
            foreach ($ssr as $ssrr) {
                $this->addSSR($ssrr);
            }
        } else if (is_string($ssr)) {
            $this->addSSR(new SSR($ssr));
        } else if ($ssr instanceof SSR) {
            $this->addSSR($ssr);
        }
    }

    public function addSSR($ssr)
    {
        if ($ssr instanceof SSR) {
            $this->SSRs[] = $ssr;
        }
    }

    public function __toString()
    {
        $res = '';
        foreach ($this->SSRs as $ssr) {
            $ssr->group = $this->name;
            $res .= (string) $ssr . "\r\n";
        }
        return Util::urlsafe_b64encode(trim($res));
    }
}
