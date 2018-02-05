<?php

namespace CnOAuth\Provider;

/**
 * 微信网页登录提供者
 */
class WechatWeb extends Wechat
{
    public function getBaseAuthorizationUrl()
    {
        return 'https://open.weixin.qq.com/connect/qrconnect';
    }

    public function getAuthorizationUrl(array $options = [])
    {
        $url = parent::getAuthorizationUrl($options);

        return $url.'#wechat_redirect';
    }
}
