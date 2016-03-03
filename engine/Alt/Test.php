<?php

// php5.2 support
if(!function_exists('array_union')){
    function array_union($array1, $array2)
    {
        $array1 = is_array($array1) ? $array1 : array();
        $array2 = is_array($array2) ? $array2 : array();
        $union = $array1;

        foreach ($array2 as $key => $value) {
            if (false === array_key_exists($key, $union)) {
                $union[$key] = $value;
            }
        }

        return $union;
    }
}

class Alt_Test extends PHPUnit_Framework_TestCase {
    public $url = "";
    public $route = "";
    public $api;

    public function connect($url, $data = array()){
        $this->api = new Alt_Api($this->url, $this->route);
        $this->api->url = $this->url;
        $this->api->route = $this->route;
        return $this->api->connect($url, $data);
    }

    public function testDummy(){}
}