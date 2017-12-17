<?php

namespace CnOAuth\Provider;

interface ResourceOwnerInterface
{
    /**
     * 返回用户标识
     *
     * @return mixed
     */
    public function getId();

    /**
     * 返回用户信息数组
     *
     * @return array
     */
    public function toArray();
}
