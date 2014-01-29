<?php
require '../classes/BlogPad.php';
require '../classes/User.php';

session_start();

if( !User::logged_in() ) {
    header('Location: '.BlogPad::get_blog_homepage());
    exit;
}

User::logout( BlogPad::get_blog_homepage() );
?>