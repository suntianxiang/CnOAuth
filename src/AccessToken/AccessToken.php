<?php

namespace CnOAuth\AccessToken;

use InvalidArgumentException;
use Exception;
use JsonSerializable;

class AccessToken implements JsonSerializable
{
    /**
     * @var string
     */
    protected $accessToken;

    /**
     * @var string
     */
    protected $resourceOwnerId;

    /**
     * @var int
     */
    protected $expiresIn;

    /**
     * @var int
     */
    protected $expires;

    /**
     * @var string
     */
    protected $refreshToken;

    /**
     * @var int
     */
    protected $refreshTokenExpiresIn;

    /**
     * @var array
     */
    protected $values = [];

    /**
     * 构造access token实体
     * @param array 属性 必须包含 "access_token"
     * @throws InvalidArgumentException if `access_token` is not provided in `$$attributes`.
     */
    public function __construct(array $attributes = [])
    {
        if (empty($attributes['access_token'])) {
            throw new InvalidArgumentException('missing "access_token"');
        }

        $this->accessToken = $attributes['access_token'];

        if (!empty($attributes['refresh_token'])) {
            $this->refreshToken = $attributes['refresh_token'];
        }

        if (isset($attributes['expires_in'])) {
            if (!is_numeric($attributes['expires_in'])) {
                throw new InvalidArgumentException('expires_in value must be an integer');
            }

            $this->expiresIn = $attributes['expires_in'];

            $this->expires = $attributes['expires_in'] != 0 ? time() + $attributes['expires_in'] : 0;
        }

        if (isset($attributes['refresh_token_expires_in'])) {
            if (!is_numeric($attributes['refresh_token_expires_in'])) {
                throw new InvalidArgumentException('expires_in value must be an integer');
            }

            $this->refreshTokenExpiresIn = $attributes['refresh_token_expires_in'];
        }

        if (isset($attributes['resource_owner_id'])) {
            $this->resourceOwnerId = $attributes['resource_owner_id'];
        }

        $this->values = array_diff_key($attributes, array_flip([
            'access_token',
            'resource_owner_id',
            'refresh_token',
            'expires_in',
            'refresh_token_expires_in',
        ]));
    }

    /**
     * Returns the access token string of this instance.
     *
     * @return string
     */
    public function getToken()
    {
        return $this->accessToken;
    }

    /**
     * Returns the refresh token, if defined.
     *
     * @return string|null
     */
    public function getRefreshToken()
    {
        return $this->refreshToken;
    }

    /**
     * Returns the expiration timestamp, if defined.
     *
     * @return integer|null
     */
    public function getExpiresIn()
    {
        return $this->expiresIn;
    }

    /**
     * Returns the expiration timestamp, if defined.
     *
     * @return integer|null
     */
    public function getRefreshTokenExpiresIn()
    {
        return $this->refreshTokenExpiresIn;
    }

    /**
     * Returns the resource owner identifier, if defined.
     *
     * @return string|null
     */
    public function getUserId()
    {
        return $this->resourceOwnerId;
    }

    /**
     * Checks if this token has expired.
     *
     * @return boolean true if the token has expired, false otherwise.
     * @throws RuntimeException if 'expires' is not set on the token.
     */
    public function hasExpired()
    {
        $expires = $this->getExpires();
        if (empty($expires)) {
            throw new RuntimeException('"expires" is not set on the token');
        }
        return $expires < time();
    }

    /**
     * Returns additional vendor values stored in the token.
     *
     * @return array
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * Returns the token key.
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getToken();
    }

    /**
     * Returns an array of parameters to serialize when this is serialized with
     * json_encode().
     *
     * @return array
     */
    public function jsonSerialize()
    {
        $parameters = $this->values;

        if ($this->accessToken) {
            $parameters['access_token'] = $this->accessToken;
        }

        if ($this->refreshToken) {
            $parameters['refresh_token'] = $this->refreshToken;
        }

        if ($this->expiresIn) {
            $parameters['expires_in'] = $this->expiresIn;
        }

        if ($this->resourceOwnerId) {
            $parameters['user_id'] = $this->resourceOwnerId;
        }

        return $parameters;
    }

    public function toArray()
    {
        return [
            'access_token' => $this->accessToken,
            'refresh_token' => $this->refreshToken,
            'expires_in' => $this->expires_in,
        ];
    }

    /**
     * 获取AccessToken相关属性
     *
     * @return mixed
     */
    public function __get($name)
    {
        if (isset($this->values[$name])) {
            return $this->values[$name];
        }

        throw new Exception('Try to get undefined value');
    }
}
