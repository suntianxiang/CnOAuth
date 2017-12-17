<?php

namespace CnOAuth\Provider;

use OpenApi\Eleme\Tool\Sign;
use CnOAuth\AccessToken\AccessToken;
use Psr\Http\Message\ResponseInterface;

class Eleme extends AbstractProvider
{
    public function getBaseAuthorizationUrl()
    {
        return 'https://open-api-sandbox.shop.ele.me/authorize';
        // return 'https://open-api.shop.ele.me/authorize';
    }

    public function getAuthorizationParams(array $options = [])
    {
        $data = [];
        $data['client_id'] = $this->clientId;
        $data['response_type'] = 'code';
        $data['redirect_uri'] = $this->redirectUri;
        $data['scope'] = isset($options['scope']) ? $options['scope'] :
         $this->getDefaultScopes();
        $data['state'] = mt_rand(1000, 9999);
        $data = array_merge($data, $options);

        return $data;
    }

    public function getBaseAccessTokenUrl(array $params)
    {
        return 'https://open-api-sandbox.shop.ele.me/token';
        // return 'https://open-api.shop.ele.me/token';
    }

    protected function getAccessTokenOptions($method, $options)
    {
        $basic = base64_encode($this->clientId.":".$this->clientSecret);
        $options = [
            'headers' => [
                'Authorization' => "Basic $basic",
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            'form_params' => [
                'grant_type' => 'authorization_code',
                'code' => $options['code'],
                'redirect_uri' => $this->redirectUri,
                'client_id' => $this->clientId,
            ]
        ];

        return $options;
    }

    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        return 'https://open-api-sandbox.shop.ele.me/api/v1/';
        // return 'https://open-api.shop.ele.me/api/v1/';
    }

    protected function getResourceOwnerDetailOptions($method, array $options = [])
    {
        if ($options) {
            return [
            'headers' => [
                'Content-Type' => 'application/json; charset=utf-8',
            ],
            'json' => $options,
            ];
        }
    }

    protected function prepareAccessTokenResponse(array $response)
    {
        $response['expires_in'] = time() + $response['expires_in'];
        $response['user_id'] = null;
        $response['refresh_token_expires_in'] = strtotime('+35 days');

        return $response;
    }

    protected function getResourceOwnerDetailMethod()
    {
        return static::METHOD_POST;
    }

    public function checkResponse(ResponseInterface $response, $data)
    {
        $code = $response->getStatucCode();
        if ($code != 200) {
            throw new \Exception('Status code Error', (string) $response->getBody());
        }

        $body  = $response->getBody();
        $json = json_decode($content, true);
        if (!empty($json['error'])) {
            throw new \Exception('response Error', $json['error']);
        }
    }

    protected function getResourceOwnerDetailParams(AccessToken $accessToken)
    {
        $common = [
            'nop' => '1.0.0',
            'id' => $this->create_uuid(),
            'metas' => [
                'app_key' => $this->clientId,
                'timestamp' => time(),
            ],
            'action' => 'eleme.user.getUser',
            'token' => $accessToken->getToken(),
            'params' => [],
        ];
        $common['signature'] = Sign::sign($common, $this->clientSecret, $accessToken->getToken());
        $common['params'] = new \stdClass();
        return $common;
    }

    public function createResourceOwner(array $response, AccessToken $token)
    {
        $response = $response['result'];

        return new ElemeUser($response);
    }

    public function getDefaultScopes()
    {
        return 'all';
    }

    private function create_uuid()
    {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
}
