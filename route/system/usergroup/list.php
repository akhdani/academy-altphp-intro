<?php defined('ALT_PATH') OR exit('No direct script access allowed');

$_REQUEST['isdisplayed'] = 1;

$dbo = new System_Usergroup;
$res = $dbo->get($_REQUEST);

return $res;