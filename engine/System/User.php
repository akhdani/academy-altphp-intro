<?php defined('ALT_PATH') or die('No direct script access.');

class System_User extends Alt_Dbo {

    public function __construct() {
        // call parent constructor
        parent::__construct();

        // define this class specific properties
        $this->pkey             = "userid";
        $this->table_name       = "sys_user";
        $this->table_fields     = array(
            "userid"            => "",
            "username"          => "",
            "password"          => "",
            "name"              => "",
            "address"           => "",
            "email"             => "",
            "phone"             => "",
            "pekerjaan"         => "",
            "jabatan"           => "",
            "rt"                => "",
            "rw"                => "",
            "kodepos"           => "",
            "kewarganegaraan"   => "",
            "wilayahid"         => "",
            "teleponrumah"      => "",
            "usergroupid"       => "",
            "isenabled"         => "",
        );

        $this->view_name            = "view_sys_user";
        $this->view_fields          = array_merge($this->table_fields, array(
            "usergroupname"         => "",
            "userlevel"             => "",
            "isdisplayed"           => "",
            "isallowregistration"   => "",
            "isloggedin"            => "",
        ));
    }
}