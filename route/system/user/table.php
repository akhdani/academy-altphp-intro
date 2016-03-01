<?php defined('ALT_PATH') OR exit('No direct script access allowed');

$_REQUEST['isdisplayed'] = 1;

$dbo = new System_User();
$total = $dbo->count($_REQUEST);

$data = $dbo->get($_REQUEST);
foreach($data as $i => $item){
    $data[$i]['DB_ROWNUM'] = ($_REQUEST['offset'] ?: 0) + $i + 1;
    unset($data[$i]['password']);
}

return array(
    'total' => $total,
    'list' => $data
);