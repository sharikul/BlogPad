<?php

// This script is used to generate a preview on the post editing page. Shouldn't be used as a standalone.

$classes_dir = '../../../classes';

include "$classes_dir/BlogPad.php";
include "$classes_dir/User.php";
include "$classes_dir/BP_Parser.php";
include "$classes_dir/Admin.php";
include "$classes_dir/Parsedown.php";
include "$classes_dir/Link_Parser.php";

extract(BlogPad::extract_globs());

$post = array(
    'title' => stripslashes( trim($_POST['title']) ),
    'post' => stripslashes( Parsedown::instance()->parse($_POST['content']) ),
    'description' => stripslashes( $_POST['description'] ),
    'date' => ( trim($_POST['date']) !== '' ) ? $_POST['date']: strtotime( date('Y-m-d G:i:s') ),
    'slug' => $_POST['slug'],
    'updated' => strtotime( date('Y-m-d G:i:s') ),
    'categories' => BlogPad::list_to_ser($_POST['categories']),
    'author' => (trim($_POST['author']) !== '') ? $_POST['author']: User::get_info('username')
);

$stylesheet = $pointers['STYLESHEET'];

include $pointers['POST'];
?>