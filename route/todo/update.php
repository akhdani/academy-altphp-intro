<?php defined('ALT_PATH') OR die('No direct access allowed.');

Alt_Validation::instance()
    ->rule(Alt_Validation::not_empty($_REQUEST['itemid']), 'Item belum dipilih!')
    ->rule(Alt_Validation::not_empty($_REQUEST['description']), 'Deskripsi harus diisi!')
    ->check();

$dbo = new Todo_Item();

return $dbo->update($_REQUEST);