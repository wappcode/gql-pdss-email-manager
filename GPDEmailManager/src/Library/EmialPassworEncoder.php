<?php

namespace GPDEmailManager\Library;


class EmialPassworEncoder
{

    const ENCRYPTION_METHOD = 'aes-256-cbc';


    public static function encrypt(string $data, string $password, $iv)
    {
        $encriptPassword = base64_encode($password);
        $iv = base64_encode($iv);
        $result = openssl_encrypt($data, static::ENCRYPTION_METHOD, $encriptPassword, 0, $iv);
        return $result;
    }
    public static function decrypt(string $data, string $password, $iv)
    {
        $encriptPassword = base64_encode($password);
        $iv = base64_encode($iv);
        $result = openssl_decrypt($data, static::ENCRYPTION_METHOD, $encriptPassword, 0, $iv);
        return $result;
    }
}
