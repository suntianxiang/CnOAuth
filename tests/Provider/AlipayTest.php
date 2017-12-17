<?php

use PHPUnit\Framework\TestCase;
use CnOAuth\Provider\Alipay;
use CnOAuth\AccessToken\AccessToken;

class AlipayTest extends TestCase
{
    protected $alipay = null;

    public function setUp()
    {
        $this->alipay = new Alipay([
            'clientId' => '2017090408550236',
            'redirectUri' => 'xxx',
            'gatewayUrl' => 'https://openapi.alipay.com/gateway.do',
            'rsaPrivateKey' => '',
            'alipayrsaPublicKey' => '',
            'apiVersion' => '1.0',
            'signType' => 'RSA2',
            'postCharset' => 'UTF-8',
            'format' => 'json'
        ]);
    }

    public function testGetBaseAuthorizationUrl()
    {
        $url = $this->alipay->getBaseAuthorizationUrl();

        $this->assertEquals($url, 'https://openauth.alipay.com/oauth2/publicAppAuthorize.htm');
    }

    public function testGetAuthorizationParams()
    {
        $result = $this->alipay->getAuthorizationParams([]);

        $this->assertArrayHasKey('app_id', $result);
        $this->assertArrayHasKey('redirect_uri', $result);
        $this->assertArrayHasKey('scope', $result);
    }

    public function testGetBaseAccessTokenUrl()
    {
        $url = $this->alipay->getBaseAccessTokenUrl([]);

        $this->assertEquals($url, 'https://openapi.alipay.com/gateway.do');
    }

    public function testGetResourceOwnerDetailsUrl()
    {
        $access_token = new AccessToken([
            'access_token' => 'asdasdasd',
            'refresh_token' => 'dasdasdasdasd',
            'expires_in' => 7200,
            'refresh_token_expires_in' => 86400,
            'resource_owner_id' => 0
        ]);
        $url = $this->alipay->getResourceOwnerDetailsUrl($access_token);

        $this->assertNotEquals(-1, strpos($url, 'https://openapi.alipay.com/gateway.do?'));
    }

    public function testCreateResourceOwner()
    {
        $response = [
            'alipay_user_info_share_response' => [
                'user_id' => '1001',
                'nick_name' => 'assda',
                'avatar' => 'xxx',
                'gender' => 'F'
            ]
        ];

        $access_token = new AccessToken([
            'access_token' => 'asdasdasd',
            'refresh_token' => 'dasdasdasdasd',
            'expires_in' => 7200,
            'refresh_token_expires_in' => 86400,
            'resource_owner_id' => 0
        ]);

        $owner = $this->alipay->createResourceOwner($response, $access_token);

        $this->assertInstanceOf(CnOAuth\Provider\AlipayUser::class, $owner);
    }


    /**
     * @expectedException \Exception
     */
    public function testCheckResponse()
    {
        $this->alipay->checkResponse([
            'error_response' => [
                'msg' => 'error message',
                'code' => 1001,
            ],
        ], []);
    }
}
