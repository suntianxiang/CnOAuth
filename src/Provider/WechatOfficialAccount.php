<?php

namespace CnOAuth\Provider;

/**
 * 微信公众号提供者
 */
class WechatOfficialAccount extends Wechat
{
    public function getBaseAuthorizationUrl()
    {
        return 'https://open.weixin.qq.com/connect/oauth2/authorize';
    }

    public function getAuthorizationUrl(array $options = [])
    {
        $url = parent::getAuthorizationUrl($options);

        return $url.'#wechat_redirect';
    }
}
