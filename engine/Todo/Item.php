<?php defined('ALT_PATH') or die('No direct script access.');

class Todo_Item extends Alt_Dbo {

    public function __construct() {
        // call parent constructor
        parent::__construct();

        // define this class specific properties
        $this->pkey             = "itemid";
        $this->table_name       = "todo_item";
        $this->table_fields     = array(
            "itemid"            => "",
            "description"       => "",
            "isfinish"          => "",
        );
    }
}