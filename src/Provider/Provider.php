<?php

namespace CnOAuth\Provider;

use CnOAuth\AccessToken\AccessToken;
use Psr\Http\Message\ResponseInterface;

/**
 * OAuth2 服务提供者接口
 */
interface Provider
{
    /**
     * 获取 authorizing 地址
     */
    public function getBaseAuthorizationUrl();

    /**
     * 获取 access token 地址
     *
     * @param array
     */
    public function getBaseAccessTokenUrl(array $params);

    /**
     * 获取 用户详细信息 地址
     *
     * @param AccessToken access token
     */
    public function getResourceOwnerDetailsUrl(AccessToken $token);

    /**
     * 获取默认scope
     */
    public function getDefaultScopes();

    /**
     * Checks a provider response for errors.
     *
     * @throws IdentityProviderException
     * @param  array $response
     * @param  array|string $data Parsed response data
     * @return void
     */
    public function checkResponse($response, $data);

    /**
     * 生成用户详细信息对象
     *
     * @param  array $response
     * @param  AccessToken $token
     * @return ResourceOwnerInterface
     */
    public function createResourceOwner(array $response, AccessToken $token);
}
