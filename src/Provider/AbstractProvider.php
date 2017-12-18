<?php

namespace CnOAuth\Provider;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\BadResponseException;
use UnexpectedValueException;
use CnOAuth\AccessToken\AccessToken;
use CnOAuth\Grant\AbstractGrant;

/**
 * 服务提供者抽象类
 */
abstract class AbstractProvider implements Provider
{
    /**
     * @var string HTTP method used to fetch access tokens.
     */
    const METHOD_GET = 'GET';

    /**
     * @var string HTTP method used to fetch access tokens.
     */
    const METHOD_POST = 'POST';

    /**
     * @var string 客户端id
     */
    protected $clientId;

    /**
     * @var string 客户端令牌
     */
    protected $clientSecret;

    /**
     * @var string 重定向地址
     */
    protected $redirectUri;

    /**
     * @var string
     */
    protected $state;

    /**
     * @var string 授权码字段名称
     */
    protected $codeName = 'code';

    /**
     * 构造器
     */
    public function __construct(array $options = array())
    {
        foreach ($options as $option => $value) {
            if (property_exists($this, $option)) {
                $this->{$option} = $value;
            }
        }
    }

    /**
     * 获取授权地址
     *
     * @return string
     */
    public function getAuthorizationUrl(array $options = [])
    {
        $url = $this->getBaseAuthorizationUrl();
        $params = $this->getAuthorizationParams($options);

        return $url.'?'.http_build_query($params);
    }

    public function getAuthorizationParams(array $options = [])
    {
        return $options;
    }

    public function getGrant($type)
    {
        switch ($type) {
            case 'authorization':
                return $this->getAuthorizationGrant();
            default:
                throw new \Exception('unknow grant type');
        }
    }

    abstract public function getAuthorizationGrant();


    /**
     * 获取access_token
     *
     * @return array access_token
     * — access_token: 访问令牌
     * — user_id: 用户id
     * - expires_in: 令牌有效时间
     * - refresh_token: 刷新令牌有
     * - refresh_token_expires_in: 刷新令牌有效时间
     */
    public function getAccessToken($grant, array $options = [])
    {
        $this->verifyGrant($grant);

        $params = [
            'client_id'     => $this->clientId,
            'client_secret' => $this->clientSecret,
            'redirect_uri'  => $this->redirectUri,
        ];

        $params   = $grant->prepareRequestParameters($params, $options);
        $url = $this->getBaseAccessTokenUrl($options);
        $method = $this->getAccessTokenMethod();
        $options  = $this->getAccessTokenOptions($method, $params);
        $request = new Request($method, $url);
        $response = $this->getParsedResponse($request, $options);
        $this->checkResponse($response, []);
        $prepared = $this->prepareAccessTokenResponse($response);
        $token    = $this->createAccessToken($prepared, $grant);

        return $token;
    }

    protected function getAccessTokenMethod()
    {
        return self::METHOD_POST;
    }

    protected function getAccessTokenOptions($method, $options)
    {
        if ($method == self::METHOD_POST) {
            return [
                'form_params' => $options
            ];
        } else {
            return [
                'query' => $options
            ];
        }
    }

    protected function prepareAccessTokenResponse(array $response)
    {
        return $response;
    }

    /**
     * 发送请求 返回响应
     *
     * @return ResponseInterface
     * @throws UnexpectedValueException 如果响应不是json
     */
    protected function getParsedResponse(RequestInterface $request, array $options = [])
    {
        $client = new Client();
        try {
            $response = $client->send($request, $options);
        } catch (BadResponseException $e) {
            $response = $e->getResponse();
        }

        if ($response->getStatusCode() == 500) {
            throw new UnexpectedValueException(
                'An OAuth server error was encountered that did not contain a JSON body',
                0,
                $e
            );
        }

        $content = $response->getBody();
        $json = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new UnexpectedValueException(sprintf(
                "Failed to parse JSON response: %s",
                json_last_error_msg()
            ));
        }

        return $json;
    }

    /**
     * Creates an access token from a response.
     *
     * The grant that was used to fetch the response can be used to provide
     * additional context.
     *
     * @param  array $response
     * @param  AbstractGrant $grant
     * @return AccessToken
     */
    protected function createAccessToken(array $response, AbstractGrant $grant)
    {
        return new AccessToken($response);
    }

    /**
     * 获取用户信息
     *
     * @return array
     */
    public function getResourceOwner(AccessToken $accessToken)
    {
        $url = $this->getResourceOwnerDetailsUrl($accessToken);
        $method = $this->getResourceOwnerDetailMethod();
        $params = $this->getResourceOwnerDetailParams($accessToken);
        $options = $this->getResourceOwnerDetailOptions($method, $params);
        $request = new Request($method, $url);
        $data = $this->getParsedResponse($request, $options);

        return $this->createResourceOwner($data, $accessToken);
    }

    protected function getResourceOwnerDetailMethod()
    {
        return self::METHOD_GET;
    }

    protected function getResourceOwnerDetailParams(AccessToken $accessToken)
    {
        return [
            'access_token' => $accessToken->getToken()
        ];
    }

    protected function getResourceOwnerDetailOptions($method, array $options = [])
    {
        if ($method == self::METHOD_POST) {
            return [
                'form_params' => $options
            ];
        } else {
            return [
                'query' => $options
            ];
        }
    }

    /**
     * 验证grant type
     */
    public function verifyGrant()
    {
    }

    abstract public function getBaseAuthorizationUrl();

    abstract public function getBaseAccessTokenUrl(array $params);

    abstract public function getResourceOwnerDetailsUrl(AccessToken $token);

    abstract public function getDefaultScopes();

    abstract public function getUserScope();

    abstract public function checkResponse($response, $data);

    /**
     * 生成用户详细信息对象
     *
     * @param  array $response
     * @param  AccessToken $token
     * @return ResourceOwnerInterface
     */
    abstract public function createResourceOwner(array $response, AccessToken $token);

    /**
     * 设置state
     *
     * @return void
     */
    public function setState($value)
    {
        $this->state = $value;
    }

    public function createRequest($method, $uri, $options = [])
    {
    }

    /**
     * 获取state
     *
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    public function getCode()
    {
        return isset($_GET[$this->codeName]) ? $_GET[$this->codeName] : null;
    }

    public function getClientId()
    {
        return $this->clientId;
    }

    public function getSecret()
    {
        return $this->clientSecret;
    }
}
