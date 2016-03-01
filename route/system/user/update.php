<?php defined('ALT_PATH') OR exit('No direct script access allowed');

$dbo = new System_User();

Alt_Validation::instance()
    ->rule(Alt_Validation::required($_REQUEST[$dbo->pkey]), "User id tidak boleh kosong!")
    ->check();

if(isset($_REQUEST["password"]))
    $_REQUEST["password"] = md5($_REQUEST["newpassword"] ? $_REQUEST["newpassword"] : $_REQUEST["password"]);

return $dbo->update($_REQUEST);