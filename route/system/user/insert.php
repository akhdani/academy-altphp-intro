<?php defined('ALT_PATH') OR exit('No direct script access allowed');

$dbo = new System_User();

// validasi
$validate = Alt_Validation::instance()
    ->rule(Alt_Validation::required($_REQUEST["username"]), "Username tidak boleh kosong!")
    ->rule(Alt_Validation::required($_REQUEST["password"]), "Password tidak boleh kosong!")
    ->rule(Alt_Validation::required($_REQUEST["name"]), "Nama tidak boleh kosong!")
    ->rule(Alt_Validation::required($_REQUEST["usergroupid"]), "Pilih usergroup terlebih dahulu!")
    ->validate();

// ubah password
$_REQUEST["password"] = md5($_REQUEST["password"]);

return $dbo->insert($_REQUEST);