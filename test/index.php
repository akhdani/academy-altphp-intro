<?php defined('ALT_PATH') or die('No direct script access.');

class IndexTest extends Alt_Test
{
    public $url = "http://localhost/academy-altphp-intro/";

    public function testOutput() {
        $output = $this->connect('index');
        $this->assertEquals("Hello World!", $output, "Output harus menghasilkan 'Hello World!'");
    }
}