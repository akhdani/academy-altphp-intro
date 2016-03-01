<?php defined('ALT_PATH') OR die('No direct access allowed.');

Alt_Validation::instance()
    ->rule(Alt_Validation::not_empty($_REQUEST['itemid']), 'Item belum dipilih!')
    ->check();

$dbo = new Todo_Item();

return $dbo->delete($_REQUEST);