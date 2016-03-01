<?php defined('ALT_PATH') OR exit('No direct script access allowed');

$dbo = new System_File();

// validasi
$validate = Alt_Validation::instance()
    ->rule(Alt_Validation::required($_REQUEST["name"]), "Nama tidak boleh kosong!")
    ->rule(Alt_Validation::required($_REQUEST["description"]), "Deskripsi tidak boleh kosong!")
    ->rule(Alt_Validation::required($_FILES['file']), "File tidak ditemukan!")
    ->validate();

$fileid = $dbo->insert($_REQUEST);

$dbo->upload(array(
    'srctable' => 'master_file',
    'srcid' => $fileid,
    'fileid' => $fileid
), $_FILES['file']);

return $fileid;