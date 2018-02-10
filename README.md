# CnOAuth
-------------
一个简单、开箱即用的OAuth2客户端

[![TravisCI](https://travis-ci.org/suntianxiang/CnOAuth.svg)](https://travis-ci.org/suntianxiang/CnOAuth)
### supported list 支持列表
1. alipay 支付宝
2. wechat 微信 公众号 网页登录
3. weibo 微博
4. eleme 饿了么商户
----------------
### requires
- php >= 7.0
- openssl (if using alipay)
- guzzlehttp ^6.3
### install 安装

using composer

```shell
composer require suntianxiang/cn-oauth
```
### usage 使用
1. wechat 微信

redirect.php
```php
    # 微信
    $wechat = new \CnOAuth\Provider\WechatOfficialAccount([
        'clientId' => 'you client id',
        'clientSecret' => 'you client secret',
        'redirectUri' => 'redirect uri'
    ]);

    $url = $wechat->getAuthorizationUrl([
        'scope' => $wechat->getDefaultScopes(),
        'state' => 'state'
    ]);

    header('Location: '.$url);
```
callback.php
```php
    # 微信
    $wechat = new \CnOAuth\Provider\WechatOfficialAccount([
        'clientId' => 'you client id',
        'clientSecret' => 'you client secret',
        'redirectUri' => 'redirect uri'
    ]);
    $grant = $wechat->getGrant('authorization');

    if ($grant->getCode()) {
        $access_token = $wechat->getAccessToken($grant);

        $owner = $wechat->getResourceOwner($access_token);

        print_r($owner->toArray());
    } else {
        // 用户取消授权
    }
```
2. alipay 支付宝

redirect.php
```php
$alipay = new \CnOAuth\Provider\Alipay([
    'gatewayUrl' => 'https://openapi.alipay.com/gateway.do',
    'clientId' => '2017090408550236',
    'rsaPrivateKey' => 'your private key',
    'alipayrsaPublicKey' => 'your alipay public key [支付宝公钥]',
    'apiVersion' => '1.0',
    'signType' => 'RSA2',
    'postCharset' => 'UTF-8',
    'format' => 'json',
    'redirectUri' => 'you callback url'
]);

$url = $alipay->getAuthorizationUrl([
    'scope' => $provider->getDefaultScopes(),
    'state' => mt_rand(1000, 9999)
]);

header('Location: '.$url);
```
callback.php
```php
    $alipay = new \CnOAuth\Provider\Alipay([
        'gatewayUrl' => 'https://openapi.alipay.com/gateway.do',
        'clientId' => '2017090408550236',
        'rsaPrivateKey' => 'your private key',
        'alipayrsaPublicKey' => 'your alipay public key [支付宝公钥]',
        'apiVersion' => '1.0',
        'signType' => 'RSA2',
        'postCharset' => 'UTF-8',
        'format' => 'json',
        'redirectUri' => 'you callback url'
    ]);

    $grant = $alipay->getGrant('authorization');

    if ($grant->getCode()) {
        $access_token = $alipay->getAccessToken($grant);

        $owner = $alipay->getResourceOwner($access_token);

        print_r($owner->toArray());
    } else {
        // user cancel auth
    }
```
### Define Provider 定义自己的提供者
    1. create a provider 继承AbstractProvider
        See CnOAuth\Provider\AbstractProvider.php
            CnOAuth\Provider\ResourceOwnerInterface.php
    2. create a grant type 继承AbstractGrant
        See CnOAuth\Grant\AbstractGrant
    3. implements ResourceOwnerInterface
