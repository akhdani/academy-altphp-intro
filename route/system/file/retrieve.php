<?php defined('ALT_PATH') OR exit('No direct script access allowed');

$dbo = new System_File();

// validasi
Alt_Validation::instance()
    ->rule(Alt_Validation::required($_REQUEST[$dbo->pkey]), "Pilih file terlebih dahulu!")
    ->check();

return $dbo->retrieve($_REQUEST);