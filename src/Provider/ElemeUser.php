<?php

namespace CnOAuth\Provider;

class ElemeUser extends ResourceOwner
{
    public function getId()
    {
        return $this->data['userId'];
    }

    public function getUserName()
    {
        return $this->data['userName'];
    }


    public function getStores()
    {
        return $this->data['authorizedShops'];
    }
}
