<?php defined('ALT_PATH') OR die('No direct access allowed.');

class Alt_Api {

    public $url;

    public function count($data, $setting = array()){
        return $this->connect('count', $data, $setting);
    }

    public function get($data, $setting = array()){
        return $this->connect('list', $data, $setting);
    }

    public function retrieve($data, $setting = array()){
        return $this->connect('retrieve', $data, $setting);
    }

    public function keyvalues($data, $setting = array()){
        return $this->connect('keyvalues', $data, $setting);
    }

    public function isexist($data, $setting = array()){
        return $this->connect('isexist', $data, $setting);
    }

    public function remove($data, $setting = array()){
        return $this->connect('remove', $data, $setting);
    }

    public function connect($url, $data = array(), $setting = array()){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url . '/' . $url);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        $data = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return ($httpcode>=200 && $httpcode<300) ? $data : false;
    }
}

