<?php

namespace CnOAuth\Grant;

class WxAuthroizationCode extends AuthroizationCode
{
    public function prepareRequestParameters(array $defaults, array $options)
    {
        $options['appid'] = $defaults['client_id'];
        $options['secret'] = $defaults['client_secret'];
        $options['grant_type'] = $this->getName();
        $options['code'] = $this->getCode();

        return $options;
    }

    public function getCode()
    {
        return isset($_GET['code']) ? $_GET['code'] : null;
    }
}
