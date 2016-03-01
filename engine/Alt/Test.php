<?php

class Alt_Test extends PHPUnit_Framework_TestCase
{
    public $url = "http://localhost/academy-altphp-intro/";
    public $route = "";

    public function connect($url, $data = array()){
        //url-ify the data for the POST
        $fields_string = "";
        foreach($data as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
        rtrim($fields_string, '&');

        //open connection
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);

        //set the url, number of POST vars, POST data
        curl_setopt($ch,CURLOPT_URL, $this->url . $this->route . $url);
        if(count($data) > 0) {
            curl_setopt($ch, CURLOPT_POST, count($data));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        }

        //execute post
        $response = curl_exec($ch);
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($response, 0, $header_size);
        $body = substr($response, $header_size);

        //close connection
        curl_close($ch);

        return json_decode($body, true);
    }
}