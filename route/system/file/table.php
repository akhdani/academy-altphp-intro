<?php defined('ALT_PATH') OR exit('No direct script access allowed');


$dbo = new System_File();
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