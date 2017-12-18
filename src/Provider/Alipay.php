<?php

namespace CnOAuth\Provider;

use CnOAuth\Tool\AlipaySign;
use CnOAuth\AccessToken\AccessToken;
use Psr\Http\Message\ResponseInterface;
use CnOAuth\Grant\AlipayAuthorizationCode;

class Alipay extends AbstractProvider
{
    use AlipaySign;

    public function getBaseAuthorizationUrl()
    {
        return 'https://openauth.alipay.com/oauth2/publicAppAuthorize.htm';
    }

    public function getAuthorizationParams(array $options = [])
    {
        $data = [];
        $data['app_id'] = $this->clientId;
        $data['redirect_uri'] = $this->redirectUri;
        $data['scope'] = isset($options['scope']) ? $options['scope'] : $this->getDefaultScopes();
        $data = array_merge($data, $options);

        return $data;
    }

    public function getBaseAccessTokenUrl(array $params)
    {
        return 'https://openapi.alipay.com/gateway.do';
    }


    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        $common = $this->getCommonParams();
        $common['method'] = 'alipay.user.info.share';
        $common['auth_token'] = $token->getToken();
        $common['sign'] = $this->sign($common);
        return 'https://openapi.alipay.com/gateway.do?'.http_build_query($common);
    }

    public function createResourceOwner(array $response, AccessToken $token)
    {
        $response = $response['alipay_user_info_share_response'];

        return new AlipayUser($response);
    }

    public function checkResponse($response, $data)
    {
        if (isset($response['error_response'])) {
            throw new \Exception($response['error_response']['msg'], $response['error_response']['code']);
        }
    }

    public function getAuthorizationGrant()
    {
        return new AlipayAuthorizationCode();
    }

    public function getDefaultScopes()
    {
        return 'auth_base';
    }

    public function getUserScope()
    {
        return 'auth_user';
    }

    public function getCommonParams()
    {
        $data = [
            'app_id' => $this->clientId,
            'format' => 'JSON',
            'charset' => 'utf-8',
            'sign_type' => 'RSA2',
            'timestamp' => date('Y-m-d H:i:s'),
            'version' => "1.0",
        ];

        return $data;
    }

    protected function getAccessTokenOptions($method, $options)
    {
        $options['method'] = 'alipay.system.oauth.token';
        $options['sign'] = $this->sign(array_merge($this->getCommonParams(), $options));
        $options =  parent::getAccessTokenOptions($method, $options);
        $options['query'] = $this->getCommonParams();

        return $options;
    }

    protected function prepareAccessTokenResponse(array $response)
    {
        $result = $response['alipay_system_oauth_token_response'];
        $result['refresh_token_expires_in'] = $result['re_expires_in'];
        $result['resource_owner_id'] = $result['user_id'];

        return $result;
    }

    protected function getResourceOwnerDetailMethod()
    {
        return static::METHOD_POST;
    }

    protected function getResourceOwnerDetailParams(AccessToken $accessToken)
    {
        return [];
    }
}
