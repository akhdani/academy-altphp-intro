<?php defined('ALT_PATH') OR exit('No direct script access allowed');

$dbo = new System_User();

$validate = Alt_Validation::instance()
    ->rule(Alt_Validation::required($_REQUEST[$dbo->pkey]), "User id tidak boleh kosong!")
    ->validate();

if(!$validate[0]) throw new Alt_Exception($validate[1]);

return $dbo->delete($_REQUEST);