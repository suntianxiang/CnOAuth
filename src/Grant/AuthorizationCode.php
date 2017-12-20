<?php

namespace CnOAuth\Grant;

abstract class AuthorizationCode extends AbstractGrant
{

    /**
     * @var string $code auth code
     */
    protected $code;

    public function __construct($options = [])
    {
        $this->code = isset($options['code']) ? $options['code'] : $this->getDefaultCode();
    }

    /**
     * @inheritdoc
     */
    protected function getName()
    {
        return 'authorization_code';
    }

    public function prepareRequestParameters(array $defaults, array $options)
    {
        $options['grant_type'] = $this->getName();
        $options['code'] = isset($options['code']) ? $options['code'] : $this->getCode();

        return $options;
    }

    /**
     * get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * set code
     *
     * @param string $code
     * @return void
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * get default code
     *
     * @return string
     */
    abstract public function getDefaultCode();

    /**
     * @inheritdoc
     */
    protected function checkRequiredParameters(array $options)
    {
        return true;
    }
}
