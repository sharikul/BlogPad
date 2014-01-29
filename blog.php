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

	// Categories go in this array. Format: 'name' => 'description'
	'categories' => array(

		'Random' => 'A collection of random posts which have been plucked out of thin air.',

		'Sharikul' => 'All Posts related to sharikul.',

		'Static' => 'Static post'
	),

	// Provide a path to the static posts directory here.
	'static_posts_dir' => 'content/static_posts',

	'no_post_message' => 'Go away!',

	'posts_per_page' => 5,

	'post_sort_type' => 'desc',

	// Login accounts go here. Format: 'username' => extended info array. Should contain first and last name, username + password (crypted)
	'accounts' => array(

		'sharikul' => array(
			'firstname' => 'Sharikul',
			'lastname' => 'Islam',
			'username' => 'sharikul',
			'password' => '$1$F84.qL3.$7RVg43fG5/hwhdVLTLpxx0',
		),

		'jakirul' => array(
			'firstname' => 'Jakirul',
			'lastname' => 'Islam',
			'username' => 'jakirul',
			'password' => '$1$rr1.YF3.$CZrCQZ.jFUNyYgtGr2Iir0'
		)
	)

);

?>