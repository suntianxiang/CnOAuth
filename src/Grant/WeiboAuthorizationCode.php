<?php

namespace CnOAuth\Grant;

class WeiboAuthorizationCode extends AuthorizationCode
{
    public function prepareRequestParameters(array $defaults, array $options)
    {
        $options['client_id'] = $defaults['client_id'];
        $options['client_secret'] = $defaults['client_secret'];
        $options['grant_type'] = $this->getName();
        $options['code'] = $this->getCode();
        $options['redirect_uri'] = $defaults['redirect_uri'];

        return $options;
    }

    public function getDefaultCode()
    {
        return isset($_GET['code']) ? $_GET['code'] : null;
    }
}
