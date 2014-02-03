<?php

class User extends BlogPad {

    static function exists($username = null) {
        if( is_null($username) ) {
            trigger_error('Please provide a username to search for.', E_USER_ERROR);
            exit;
        }

        return array_key_exists($username, BlogPad::get_setting('accounts'));
    }

    static function process_login($username = null, $password = null) {

        if( User::logged_in() ) {
            trigger_error('Cannot login again.');
            return false;
        }

        if( is_null($username) || is_null($password) ) {
            trigger_error('Please provide both a username and password to process for login.', E_USER_ERROR);
            exit;
        }

        if( !User::exists($username) ) {
            return "User `$username` doesn't exist.";
        }
    }

    static function login($username, $password) {
        setcookie('user', $username, strtotime('+14 days')); 
        setcookie("user-$username-password", $password, strtotime('+14 days'));
    }

    static function get_info($specifically = null) {

        if( !User::logged_in() ) {
            return;
        }

        $accounts = BlogPad::get_setting('accounts');
        $username = $_COOKIE['user'];

        if( !array_key_exists($username, $accounts) ) {
            trigger_error("Username `$username` doesn't exist.");
            return false;
        }

        $accounts[$username]['fullname'] = $accounts[$username]['firstname'].' '.$accounts[$username]['lastname'];

        return ( is_null($specifically) ) ? $accounts[$username]: $accounts[$username][$specifically];
    }

    static function logged_in() {
        return isset($_COOKIE['user']);
    }

    static function logout($redirect) {
        if( User::logged_in() ) {

            $user = $_COOKIE['user'];

            setcookie('user', '', strtotime('-14 days'));
            setcookie("user-$user-password", '', strtotime('-14 days'));
            header("Location: $redirect");
            exit;
        }

        return false;
    }

    static function is_still_valid() {

        if( User::logged_in() ) {
            $accounts = BlogPad::get_setting('accounts');
            $user = $_COOKIE['user'];

            return self::exists($user) && isset($accounts[$user]) && $accounts[$user]['username'] = $user && $accounts[$user]['password'] === $_COOKIE["user-$user-password"];
        }
    }
}

?>