<?php

class User extends BlogPad {

    // Determines whether a session has been established beforehand.
    protected static $sessioned = false;

    static function exists($username = null) {
        if( is_null($username) ) {
            trigger_error('Please provide a username to search for.', E_USER_ERROR);
            exit;
        }

        $settings = self::get_blog_settings();

        return array_key_exists($username, $settings['accounts']);
    }

    static function process_login($username = null, $password = null) {
        if( is_null($username) || is_null($password) ) {
            trigger_error('Please provide both a username and password to process for login.', E_USER_ERROR);
            exit;
        }

        if( !self::exists($username) ) {
            return "User `$username` doesn't exist.";
        }
    }
}

?>