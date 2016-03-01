<?php defined('ALT_PATH') OR die('No direct access allowed.');

$dbo = new Todo_Item();

return $dbo->get($_REQUEST);