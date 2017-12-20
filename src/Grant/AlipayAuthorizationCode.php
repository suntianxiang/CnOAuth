<?php

namespace CnOAuth\Grant;

use CnOAuth\Tool\AlipaySign;

class AlipayAuthroizationCode extends AuthorizationCode
{
    public function getDefaultCode()
    {
        return isset($_GET['auth_code']) ? $_GET['auth_code'] : null;
    }
}
