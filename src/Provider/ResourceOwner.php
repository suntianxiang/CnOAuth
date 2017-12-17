<?php

namespace CnOAuth\Provider;

abstract class ResourceOwner implements ResourceOwnerInterface
{
    /**
     * @var array 用户信息
     */
    protected $data;

    public function __construct(array $response)
    {
        $this->data = $response;
    }

    public abstract function getId();

    public function toArray()
    {
        return $this->data;
    }
}
