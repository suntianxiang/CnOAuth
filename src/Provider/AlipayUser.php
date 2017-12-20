<?php

namespace CnOAuth\Provider;

class AlipayUser extends ResourceOwner
{
    public function getId()
    {
        return $this->data['user_id'];
    }

    public function getUserName()
    {
        return !empty($this->data['nick_name']) ? $this->data['nick_name'] : '';
    }

    public function getPicture()
    {
        return $this->data['avatar'];
    }

    public function getSex()
    {
        switch ($this->data['gender']) {
            case 'F':
                return 2;
            case 'M':
                return 1;
            default:
                return 0;
        }
    }
}
