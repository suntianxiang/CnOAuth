<?php

namespace CnOAuth\Grant;

abstract class AuthroizationCode extends AbstractGrant
{
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
        $options['code'] = $this->getCode();

        return $options;
    }

    /**
     * @inheritdoc
     */
    protected function checkRequiredParameters(array $options)
    {
        return true;
    }

    abstract public function getCode();
}
