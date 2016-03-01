<?php defined('ALT_PATH') OR exit('No direct script access allowed');

// get username and password
$username = $_REQUEST['username'] ? $_REQUEST['username'] : '';
$password = $_REQUEST['password'] ? $_REQUEST['password'] : '';

// user already login and token is still valid, return previous token
if(System_Auth::islogin()) {
    $userdata = System_Auth::get_user_data();

    // check if login using previous username, return token
    if($userdata['username'] == $username)
        return System_Auth::get_token();

    // logout
    $dbo = new System_Session();
    $res = $dbo->delete(array(
        'where' => 'userid = ' . $dbo->quote($userdata['userid']) . ' and token like ' . $dbo->quote(System_Auth::get_token())
    ));

    System_Auth::clear_token();
}

// user not logged in but token is exist, try to force logout
if(!System_Auth::islogin() && System_Auth::get_token() != ''){
    try{
        include 'logout.php';
    }catch (Exception $e){}
}

// validate username and password
Alt_Validation::instance()
    ->rule(Alt_Validation::not_empty($username), 'Username harus diisi!')
    ->rule(Alt_Validation::not_empty($password), 'Password harus diisi!')
    ->check();

// check is exist within database
$user = new System_User();
$res = $user->get(array(
    'where' => 'username = ' . $user->quote($username),
));

// user not found
if(count($res) != 1)
    throw new Alt_Exception('User tidak ditemukan!');

// set userdata
$userdata = $res[0];

// checking if password correct
if(md5($password) != $userdata['password'])
    throw new Alt_Exception('Password tidak cocok!');

unset($userdata['password']);
$token = System_Auth::generate_token($userdata);
System_Auth::save_token($token);

$session = new System_Session();
$session->insert(array(
    'userid' => $userdata['userid'],
    'token' => $token,
));

return $token;