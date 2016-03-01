<?php defined('ALT_PATH') OR exit('No direct script access allowed');

$dbo = new System_User();
return $dbo->keyvalues($_REQUEST);