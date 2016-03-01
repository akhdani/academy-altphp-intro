<?php defined('ALT_PATH') OR exit('No direct script access allowed');

// get from previous token
$token = System_Auth::get_token($_REQUEST['token']);

// validate token and get userdata
$userdata = System_Auth::get_user_data($token, true);

// get from session
$dbo = new System_Session();
$res = $dbo->get(array(
    'where' => 'userid = ' . $dbo->quote($userdata['userid']) . ' and token like ' . $dbo->quote($token)
), true);

// token already logout
if(count($res) != 1)
    throw new Alt_Exception('Token already logged out!');

// try to force logout
try{
    include 'logout.php';
}catch (Exception $e){}

// generate new token
$token = System_Auth::generate_token($userdata);
System_Auth::save_token($token);

// save to session
$session = new System_Session();
$session->insert(array(
    'userid' => $userdata['userid'],
    'token' => $token,
));

return $token;