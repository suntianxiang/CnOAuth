<?php

namespace CnOAuth\Tool;

use InvalidArgumentException;

trait AlipaySign
{
    /**
     * @var string rsa签名私钥
     */
    public $rsaPrivateKey;

    /**
     * @var string rsa签名私钥
     */
    public $rsaPublicKey;

    public function sign($data, $signType = 'RSA2')
    {
        $data['sign'] = null;
        $data = $this->makeSignContent($data);
        $sign = null;

        $priKey=$this->rsaPrivateKey;
        $primaryKey = "-----BEGIN RSA PRIVATE KEY-----\n" .
            wordwrap($priKey, 64, "\n", true) .
            "\n-----END RSA PRIVATE KEY-----";

        if ("RSA2" == $signType) {
            openssl_sign($data, $sign, $primaryKey, OPENSSL_ALGO_SHA256);
        } else {
            openssl_sign($data, $sign, $primaryKey);
        }

        return base64_encode($sign);
    }

    /**
     * 生成待签名字符串
     *
     * @param array $data
     * @return string
     */
    public function makeSignContent($data)
    {
        ksort($data);

        $signContent = '';
        foreach ($data as $key => $value) {
            if (!$this->checkEmpty($value)) {
                $signContent .= "{$key}={$value}&";
            }
        }

        return substr($signContent, 0, strlen($signContent) - 1);
    }

    /**
     * 检测值是否为空
     *
     * @param mixed $value
     * @return boolean
     */
    protected function checkEmpty($value)
    {
        if (!isset($value)) {
            return true;
        }

        if ($value === null) {
            return true;
        }

        if (trim($value) === '') {
            return true;
        }

        return false;
    }

    /**
     * 验签
     *
     * @return
     */
    public function verify(array $data)
    {
        if (!isset($data['sign'])) {
            throw new InvalidArgumentException('验签， 缺少签名');
        }

        $sign = $data['sign'];
        $sign_type = $data['sign_type'];
        $data['sign'] = null;
        $data['sign_type'] = null;

        $string = $this->makeSignContent($data);

        $pubKey= $this->alipayrsaPublicKey;
        $publicKey = "-----BEGIN PUBLIC KEY-----\n" .
            wordwrap($pubKey, 64, "\n", true) .
            "\n-----END PUBLIC KEY-----";

        if ("RSA2" == $sign_type) {
            $result = (bool)openssl_verify($data, base64_decode($sign), $publicKey, OPENSSL_ALGO_SHA256);
        } else {
            $result = (bool)openssl_verify($data, base64_decode($sign), $publicKey);
        }

        return $result;
    }
}
