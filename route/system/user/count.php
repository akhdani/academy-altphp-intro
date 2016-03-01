<?php defined('ALT_PATH') OR exit('No direct script access allowed');

$_REQUEST['isdisplayed'] = 1;

$dbo = new System_User();

return $dbo->count($_REQUEST);

