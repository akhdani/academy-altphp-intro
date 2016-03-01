<?php

class IndexTest extends Alt_Test
{
    public function testOutput() {
        $output = $this->connect('index');
        $this->assertEquals("Hello World!", $output, "Output harus menghasilkan 'Hello World!'");
    }
}