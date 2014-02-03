<?php

// This script is used to generate a preview on the post editing page. Shouldn't be used as a standalone.

session_start();
$classes_dir = '../../../classes';

include "$classes_dir/BlogPad.php";
include "$classes_dir/User.php";
include "$classes_dir/BP_Parser.php";
include "$classes_dir/Admin.php";
include "$classes_dir/Parsedown.php";
include "$classes_dir/Link_Parser.php";

extract(BlogPad::extract_globs());

parse_str($_SERVER['QUERY_STRING']);

$post = array(
    'title' => trim($title),
    'post' => Parsedown::instance()->parse($content),
    'description' => $description,
    'date' => ( trim($date) !== '' ) ? $date: strtotime( date('Y-m-d G:i:s') ),
    'slug' => $slug,
    'updated' => strtotime( date('Y-m-d G:i:s') ),
    'categories' => BlogPad::list_to_ser($categories),
    'author' => (trim($author) !== '') ? $author: User::get_info('username')
);

$stylesheet = $pointers['STYLESHEET'];

include $pointers['POST'];
?>