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
            'rsaPrivateKey' => 'MIIEpQIBAAKCAQEAwtmprmRhJC4wQ5+9FWvv95/S4p1a9B06/2viF0EwwaeninCFXIisw7FZePmDS56ML/a2YZXWMhhOJPyUovp8YLLMlc+YDqW8yNdqhkLzUGhYmc71Ml3ozFsmTMOUb3iXsFilfsljzab3v2tW/61wNfOPd28itz6ITBoKjYT20KbpROH1ie5ESyQevRmq2rEIQ6nM6prTKSM/RrEJAJGT/b4gFkDt9yjavTUu6xsLsPF4E9Zp252b2HyFcLm7hHrEvSTkEW/VqqAWGJuloGq2rrEwPt2bkv5qjWlEBNX9Njkb+LkqVAMyoxPnB0M4KaO4gWpCAS2yE+ngeEGBf98FSwIDAQABAoIBAQCnh7YYtz2+eegNfJ56eJ+ObOBI77pwAWHkksGF9QsSti+bHDvN38WLTET9eqqkreEirCELrmTIFZW6v2Cc8HlYOuMkO6UlkuLKXEy+u3mwPMdRF1xCvfOXIRfBELfjT/A8Mzu30zf4bgK9lnDqzBiaiptjuS5EF4PgytMf8p6zrg2emi9Vm1dmxiRuxh4mCoOQbv2wDGgug5a4px8HhMQyCTSfHkMDbFbeQmf3lp1lzajDwyHTKkW0LF95bqwpJN8lEpXg+v5atc100O2CfBXkSo3OWOmW2hJchgxcsGj5mSLMXLg3iPoV01K6rQxLbLHEpOZVcPfUz59RdRCTiKqBAoGBAPL2CVBLbT9J5EaJlmbpVGTae/RqaprpFYClnNv9CY035mabKEoY06XLOVQZW8rj7QqUv77bgSYvtfWeP9ZsbEiIf6hBVe6fVdpef7/Gl2HpFwgcO4KlMA19FVAU/8Owarzu3l3/G2zlelzcKR+9E6bAdmr4+BFhrAPCZ3NF5xPxAoGBAM1OpbLIqNTfrWAhJYtOfo9DE8tiZMY9M5xx8ss7cy7+oFJimrq44bVXovYpCZNJA7USlE6wwAwLztBYdE8BezYtVQYT9FMAONdYyGtxEgHiVNFtyJHHbT4fNaeilK+i+2bPRI+mzf79hnSt1oP37OP4bADIUTCnKKLAoGJTzfj7AoGBAI3RC1KxFE2y5eo+eWoMnFRaK6xZSWyuzPxQryTBqIejr3sI6hWGNFQ8MXRjcO8W59AbT2kqW1Jc2wtJmd2hX/teoZUmvfARVV2nd04Lr2VZVzZGtH3nygq1jnE2MKybF1K841NeizWfJTN61w5NoWDeSsDaHoKWzn9LRHNGxEEhAoGBALQ3rUvayBjUn6QadJJsPLwU7XNC06MQZhWWEMJFVT6TVLf8xkXRtI8ydmIOc9FN7msr+/N3Q51PYCOTAcW4Tyenc1L6gSW6tCqUTJnDj0MIIdwUulL88+/sBXU5SbtDHduJZW2Txo9mjHgvgHYnU1Jp5qRpMpu6n/j9jcvCNLOxAoGAHCh3t2qGHeA5HWdJul0UZ56JoD/Y+ADuzzL4nV56TVJwhFJu2heYMyG/ys6TfVF+iwYzdE8PTejxIyTvWw7KKbdYuun8OuNfDCdkLp4xUNSD27Htbsvsj0CKKKrclHCFOvDcCgdzxTKL8mdbgLinQprCtLB+0RyEM1Yps0cvm+s=',
            'alipayrsaPublicKey' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAwtmprmRhJC4wQ5+9FWvv95/S4p1a9B06/2viF0EwwaeninCFXIisw7FZePmDS56ML/a2YZXWMhhOJPyUovp8YLLMlc+YDqW8yNdqhkLzUGhYmc71Ml3ozFsmTMOUb3iXsFilfsljzab3v2tW/61wNfOPd28itz6ITBoKjYT20KbpROH1ie5ESyQevRmq2rEIQ6nM6prTKSM/RrEJAJGT/b4gFkDt9yjavTUu6xsLsPF4E9Zp252b2HyFcLm7hHrEvSTkEW/VqqAWGJuloGq2rrEwPt2bkv5qjWlEBNX9Njkb+LkqVAMyoxPnB0M4KaO4gWpCAS2yE+ngeEGBf98FSwIDAQAB',
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
