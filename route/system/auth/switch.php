<?php defined('ALT_PATH') OR exit('No direct script access allowed');

// only for alt and sysadmin
System_Auth::set_permission(3);

// get username and password
$username = $_REQUEST['username'] ? $_REQUEST['username'] : '';

// validate username and password
Alt_Validation::instance()
    ->rule(Alt_Validation::not_empty($username), 'Username harus diisi!')
    ->check();

// check is exist within database
$user = new System_User();
$res = $user->get(array(
    'where' => 'username = ' . $user->quote($username),
));

// user not found
if(count($res) != 1)
    throw new Alt_Exception('User tidak ditemukan!');

// login with generated
$user = $res[0];
unset($user['password']);

// do logout for admin
include "logout.php";

$token = System_Auth::generate_token($user);
System_Auth::save_token($token);

$session = new System_Session();
$session->insert(array(
    'userid' => $user['userid'],
    'token' => $token,
));

return $token;