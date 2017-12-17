<?php

namespace CnOAuth\Grant;

abstract class AbstractGrant
{
    /**
     * Returns the name of this grant, eg. 'grant_name', which is used as the
     * grant type when encoding URL query parameters.
     *
     * @return string
     */
    abstract protected function getName();

    /**
     * Prepares an access token request's parameters by checking that all
     * required parameters are set, then merging with any given defaults.
     *
     * @param  array $defaults
     * @param  array $options
     * @return array
     */
    public function prepareRequestParameters(array $defaults, array $options)
    {
        $defaults['grant_type'] = $this->getName();
        $provided = array_merge($defaults, $options);

        $this->checkRequiredParameters($provided);

        return $provided;
    }

    abstract protected function checkRequiredParameters(array $options);
}
