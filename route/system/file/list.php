<?php defined('ALT_PATH') OR exit('No direct script access allowed');

$dbo = new System_File();

return $dbo->get($_REQUEST);