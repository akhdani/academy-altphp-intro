<?php defined('ALT_PATH') OR exit('No direct script access allowed');

$dbo = new System_User();

// validasi
Alt_Validation::instance()
    ->rule(Alt_Validation::required($_REQUEST[$dbo->pkey]), "Pilih user terlebih dahulu!")
    ->check();

return $dbo->retrieve($_REQUEST);