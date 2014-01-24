<?php

// Array which stores generic information.
return array(

	'base' => $_SERVER['DOCUMENT_ROOT'].basename(__DIR__),

	// What is the theme that's being used? Set it here.
	'using' => 'default',

	'database' => array(
		'host' => 'localhost',
		'username' => 'root',
		'password' => '',
		'database' => 'blogpad_posts',
	),

	// if true, BlogPad will automatically link post title's to a URL.
	'auto_link' => true,

	// In this array is where hard-coded categories reside.
	'categories' => array(

		'Random' => array(
			'description' => 'A collection of random posts which have been plucked out of thin air.'
		),

		'Sharikul' => array(
			'description' => 'All Posts related to sharikul.'
		),

		'Static' => array(
			'description' => 'Static post'
		),

		'Rahan' => array(
			'description' => 'Rahan'
		)
	),

	// Provide a path to the static posts directory here.
	'static_posts_dir' => 'content/static_posts',

	'no_post_message' => 'Go away!',

	'posts_per_page' => 5,

	'post_sort_type' => 'desc',

	'accounts' => array(

		'sharikul' => array(
			'firstname' => 'Sharikul',
			'lastname' => 'Islam',
			'username' => 'sharikul',
			'password' => 'sex',
		)
	)

);

?>