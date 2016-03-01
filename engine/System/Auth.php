<?php defined('ALT_PATH') or die('No direct script access.');

class System_Auth {

    public static function generate_token($data){
        if(isset($data) && $data){
            $session = Alt::$config['session'];
            $data['exp'] = time() + $session['lifetime'];

            return Alt_Jwt::encode($data, Alt::$config['app_name']);
        }else{
            return '';
        }
    }

    public static function save_token($token){
        $_SESSION['token'] = $token;
    }

    public static function clear_token(){
        if($_SESSION['token'])
            unset($_SESSION['token']);
    }

    public static function get_user_data($token = '', $verify = false){
        $token = self::get_token($token);
        try{
            $userdata = Alt_Jwt::decode($token, Alt::$config['app_name'], $verify);
        }catch (Exception $e){
            $userdata = new stdClass();
            if($verify) throw new Alt_Exception('Token tidak valid!');
        }

        return (array)$userdata;
    }

    public static function get_token($token = ''){
        $token = $token ? $token : $_REQUEST['token'];
        $token = $token ? $token : $_SESSION['token'];

        return $token;
    }

    public static function set_permission($permission){
        $userdata = self::get_user_data();
        $level = $userdata['userlevel'];

        if ($level == null || ($permission === 0 && !self::islogin()))
            throw new Alt_Exception('Anda belum login atau session anda sudah habis!', Alt::STATUS_UNAUTHORIZED);
        if (!self::check($permission))
            throw new Alt_Exception('Anda tidak berhak mengakses!', Alt::STATUS_FORBIDDEN);
    }

    public static function check($permission){
        if ($permission == null) {
            return true;
        } else {
            $userdata = self::get_user_data();
            $level = (int)$userdata['userlevel'];
            return (((int)$level & (int)$permission) > 0);
        }
    }

    public static function islogin() {
        try{
            $userdata = self::get_user_data(null, true);
        }catch(Exception $e){
            $userdata = array();
        }
        return isset($userdata['userid']);
    }

}