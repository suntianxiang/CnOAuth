<?php

namespace CnOAuth\Grant;

use CnOAuth\Tool\AlipaySign;

class AlipayAuthroizationCode extends AuthroizationCode
{
    public function getCode()
    {
        return isset($_GET['auth_code']) ? $_GET['auth_code'] : null;
    }
}
