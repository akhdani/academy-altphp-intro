<?php defined('ALT_PATH') or die('No direct script access.');

class Alt_Security {
    public static function encrypt($text, $options = array()){
        if(!function_exists('mcrypt_encrypt'))
            throw new Alt_Exception("PHP Mcrypt extension is not enabled");

        $options = array_union($options, array(
            'algorithm' => MCRYPT_RIJNDAEL_128,
            'mode'      => MCRYPT_MODE_CBC,
            'key'       => Alt::$config['app']['id'],
            'iv'        => Alt::$config['app']['id'],
        ));

        return rtrim(
            base64_encode(
                mcrypt_encrypt(
                    $options['algorithm'],
                    $options['key'],
                    $text,
                    $options['mode'],
                    $options['iv'] ? $options['iv'] : mcrypt_create_iv(
                        mcrypt_get_iv_size(
                            $options['algorithm'],
                            $options['mode']
                        ),
                        MCRYPT_DEV_URANDOM)
                )
            ), "\0"
        );
    }

    public static function decrypt($text, $options = array()){
        if(!function_exists('mcrypt_decrypt'))
            throw new Alt_Exception("PHP Mcrypt extension is not enabled");

        $options = array_union($options, array(
            'algorithm' => MCRYPT_RIJNDAEL_128,
            'mode'      => MCRYPT_MODE_CBC,
            'key'       => Alt::$config['app']['id'],
            'iv'        => Alt::$config['app']['id'],
        ));

        return rtrim(
            mcrypt_decrypt(
                $options['algorithm'],
                $options['key'],
                base64_decode($text),
                $options['mode'],
                $options['iv'] ? $options['iv'] : mcrypt_create_iv(
                    mcrypt_get_iv_size(
                        $options['algorithm'],
                        $options['mode']
                    ),
                    MCRYPT_DEV_URANDOM
                )
            ), "\0"
        );
    }
}