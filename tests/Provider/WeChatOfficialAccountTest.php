<?php

use PHPUnit\Framework\TestCase;
use CnOAuth\Provider\WechatOfficialAccount;
use CnOAuth\AccessToken\AccessToken;

class WechatOfficialAccountTest extends TestCase
{
    protected $wechat = null;

    public function setUp()
    {
        $this->wechat = new WechatOfficialAccount([
            'clientId' => 'wx79a15c2f7436e93e',
            'clientSecret' => '27c17eba44bb7f458a6d14b446c39815',
            'redirectUri' => 'http://xxx.com',
        ]);
    }

    public function testGetBaseAuthorizationUrl()
    {
        $url = $this->wechat->getBaseAuthorizationUrl();

        $this->assertTrue(strpos('https://open.weixin.qq.com/connect/oauth2/authorize', $url) !== false);
    }

    public function testGetAuthorizationParams()
    {
        $result = $this->wechat->getAuthorizationParams(['state' => 123456]);

        $this->assertArrayHasKey('appid', $result);
        $this->assertArrayHasKey('redirect_uri', $result);
        $this->assertArrayHasKey('scope', $result);
        $this->assertArrayHasKey('response_type', $result);
        $this->assertArrayHasKey('state', $result);
    }

    public function testGetBaseAccessTokenUrl()
    {
        $url = $this->wechat->getBaseAccessTokenUrl([]);

        $this->assertEquals($url, 'https://api.weixin.qq.com/sns/oauth2/access_token');
    }

    public function testGetResourceOwnerDetailsUrl()
    {
        $access_token = new AccessToken([
            'access_token' => 'asdasdasd',
            'refresh_token' => 'dasdasdasdasd',
            'openid' => 123456,
            'expires_in' => 7200,
            'refresh_token_expires_in' => 86400,
            'resource_owner_id' => 0
        ]);
        $url = $this->wechat->getResourceOwnerDetailsUrl($access_token);

        $this->assertNotEquals(-1, strpos($url, 'https://api.weixin.qq.com/sns/userinfo'));
    }

    public function testCreateResourceOwner()
    {
        $response = [
            "openid" => "OPENID",
            " nickname" =>  'NICKNAME',
            "sex" => "1",
            "province" => "PROVINCE",
            "city" => "CITY",
            "country" => "COUNTRY",
            "headimgurl" =>     "http://wx.qlogo.cn/mmopen/g3MonUZtNHkdmzicIlibx6iaFqAc56vxLSUfpb6n5WKSYVY0ChQKkiaJSgQ1dZuTOgvLLrhJbERQQ4eMsv84eavHiaiceqxibJxCfHe/46",
            "privilege" => [
                "PRIVILEGE1",
                "PRIVILEGE2"
            ],
            "unionid" => "o6_bmasdasdsad6_2sgVt7hMZOPfL"
        ];

        $access_token = new AccessToken([
            'access_token' => 'asdasdasd',
            'refresh_token' => 'dasdasdasdasd',
            'expires_in' => 7200,
            'refresh_token_expires_in' => 86400,
            'resource_owner_id' => 0
        ]);

        $owner = $this->wechat->createResourceOwner($response, $access_token);

        $this->assertInstanceOf(CnOAuth\Provider\WechatUser::class, $owner);
    }

    public function testCheckResponse()
    {
        $result = $this->wechat->checkResponse([
            'errcode' => 40003,
            'errmsg' => "invalid openid"
        ], []);

        $this->assertNull($result);
    }
}
