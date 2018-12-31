<?php

class JWTController{
    function __construct()
    {
        $this->secret = 'adefemiConsult001';
    }

    function base64UrlEncode($data){
        $urlSafeData = strtr(base64_encode($data), '+/','-_');
        return rtrim($urlSafeData, '=');
    }

    /**
     * @param $data
     * @return bool|string
     */
    function base64UrlDecode($data){
        $urlUnsafeData = strtr($data, '-_', '+/');
        $paddedData = str_pad($urlUnsafeData, strlen($data) % 4, '=', STR_PAD_RIGHT);
        return base64_decode($paddedData);
    }

    function encode($payload){
        $header = json_encode(['type' => 'JWT', 'alg' => 'HS256']);

        $payload = json_encode($payload);

        $headerEncoded = $this->base64UrlEncode($header);
        $payloadEncoded = $this->base64UrlEncode($payload);

        $dataEncoded = "$headerEncoded.$payloadEncoded";

        $rawSignature = hash_hmac('sha256', $dataEncoded, $this->secret, true);

        $signatureEncoded = $this->base64UrlEncode($rawSignature);

        $jwt_token = "$dataEncoded.$signatureEncoded";

        return $jwt_token;
    }

    function verify($token){
        list($headerEncoded, $payloadEncoded, $signatureEncoded) = explode('.', $token);
        $dataEncoded = "$headerEncoded.$payloadEncoded";
        $signature = $this->base64UrlDecode($signatureEncoded);
        $rawSignature = hash_hmac('sha256', $dataEncoded, $this->secret, true);
        return $this->hash_equals($rawSignature, $signature);
    }

    function decode($token){
        list($headerEncoded, $payloadEncoded, $signatureEncoded) = explode('.', $token);
        $payload = $this->base64UrlDecode($payloadEncoded);
        return $payload;
    }

    function hash_equals($str1, $str2)
    {
        if(strlen($str1) != strlen($str2))
        {
            return false;
        }
        else
        {
            $res = $str1 ^ $str2;
            $ret = 0;
            for($i = strlen($res) - 1; $i >= 0; $i--)
            {
                $ret |= ord($res[$i]);
            }
            return !$ret;
        }
    }
}

?>