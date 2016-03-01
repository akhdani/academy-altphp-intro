<?php defined('ALT_PATH') OR die('No direct access allowed.');

return array (
    'app' => array(
        'id' => 'alt-php',
        'name' => 'Alt PHP Framework',
        'output' => 'json',
    ),
    'session' => array(
        'lifetime' => 43200,
    ),
    /*'security' => array(
        'algorithm' => MCRYPT_RIJNDAEL_128,
        'mode' => MCRYPT_MODE_CBC,
        'key' => 'u/Gu5posvwDsXUnV',
        'iv' => '5D9r9ZVzEYYgha93',
    ),*/
    'database' => array(
        'default' => array (
            'type'       => 'Mysql',
            'connection' => array(
                'hostname'   => 'localhost',
                'username'   => 'root',
                'password'   => 'w2e3r4',
                'persistent' => FALSE,
                'database'   => 'academy-altphp-intro',
            )
        ),
    ),
);