### 一个简单的OAuth2客户端

### supported list 支持列表
1. alipay
2. wechat
3. weibo
4. eleme
----------------

### install 安装
using composer
    composer require suntianxiang/cn-oauth
requires
    php >= 7.0
    openssl (if using alipay)
### usage 使用
1. authorization
```php
        $provider = new \CnOAuth\Provider\Alipay([
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

        $url = $provider->getAuthorizationUrl([
            'scope' => $provider->getDefaultScopes(),
            'state' => mt_rand(1000, 9999)
        ]);

        header('Location: '.$url);
```
2. callback
```php
    $provider = new \CnOAuth\Provider\Alipay([
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

    $grant = $provider->getGrant('authorization');

    if ($grant->getCode()) {
        $access_token = $provider->getAccessToken($grant);

        $owner = $provider->getResourceOwner($access_token);

        print_r($owner->toArray());
    } else {
        // user cancel auth
    }
```

### Define Provider
    1. create a provider
        See CnOAuth\Provider\AbstractProvider.php
            CnOAuth\Provider\ResourceOwnerInterface.php
    2. create a grant type
        See CnOAuth\Grant\AbstractGrant
    3. implements ResourceOwnerInterface
