<?php

namespace CnOAuth;

use RuntimeException;
use CnOAuth\Provider\Alipay;
use CnOAuth\Provider\Wechat;

class ProviderFactory
{
    public static function create($type)
    {
        switch ($type) {
            case 'alipay':
                $config = config('alipayNew');
                $config['clientId'] = $config['appId'];
                $config['redirectUri'] = site_url('/auth/oAuth2Login/callback');
                return new Alipay($config);
            case 'wechat':
                $config = config('wechat.mp');
                $config['clientId'] = $config['appId'];
                $config['clientSecret'] = $config['appSecret'];
                return new Wechat($config);
            default:
                throw new RuntimeException('un supported type'.$type, 1);
        }
    }
}
