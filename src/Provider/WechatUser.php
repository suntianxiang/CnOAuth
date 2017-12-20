<?php

namespace CnOAuth\Provider;

class WechatUser extends ResourceOwner
{
    public function getId()
    {
        return $this->data['openid'];
    }

    public function getUnionId()
    {
        return isset($this->data['unionid']) ? $this->data['unionid'] : null;
    }

    public function getUserName()
    {
        return !empty($this->data['nickname']) ? $this->data['nickname'] : '';
    }

    public function getPicture()
    {
        return $this->data['headimgurl'];
    }

    public function getSex()
    {
        return $this->data['sex'];
    }
}
