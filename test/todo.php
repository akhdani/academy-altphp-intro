<?php defined('ALT_PATH') or die('No direct script access.');

class TodoTest extends Alt_Test
{
    public $url = "http://localhost/academy-altphp-intro/";
    public $route = 'todo/';

    public function testCrud() {
        $itemid = $this->connect('insert', array('description' => 'abc'));
        $this->assertGreaterThan(0, $itemid, 'Insert harus mengembalikan nilai lebih dari 0');

        $item = $this->connect('retrieve', array('itemid' => $itemid));
        $this->assertEquals('abc', $item['description'], 'Deskripsi harus "abc"');

        $item['description'] = 'def';
        $update = $this->connect('update', $item);
        $this->assertEquals(1, $update, 'Update harus mengembalikan nilai 1');

        $item = $this->connect('retrieve', array('itemid' => $itemid));
        $this->assertEquals('def', $item['description'], 'Deskripsi harus "def"');

        $delete = $this->connect('delete', array('itemid' => $itemid));
        $this->assertEquals(1, $delete, 'Delete harus mengembalikan nilai 1');
    }
}