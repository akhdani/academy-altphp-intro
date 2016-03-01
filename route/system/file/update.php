<?php defined('ALT_PATH') OR exit('No direct script access allowed');

$dbo = new System_File();

Alt_Validation::instance()
    ->rule(Alt_Validation::required($_REQUEST[$dbo->pkey]), "File id tidak boleh kosong!")
    ->check();

$res = $dbo->update($_REQUEST);

if($_FILES['file'])
    $dbo->upload($_REQUEST, $_FILES['file']);

return $res;