<?php
return array(

	'base' => dirname(realpath(__FILE__)),

	// What's the name of your blog?
	'blogname' => "Sharikul's Blog",

	// How would you describe your blog?
	'blogdescription' => 'An awesome blog!',

	// Visit http://php.net/manual/en/timezones.php and set accordingly
	'timezone' => 'Europe/London',

	// What is the theme that's being used? Set it here.
	'using' => 'BlogPad',

	'database' => array(
		'host' => 'localhost', // change this accordingly
		'username' => 'root', // change this accordingly
		'password' => '', // fill this in accordingly
		'database' => '', // fill this in accordingly
	),

	// if true, BlogPad will automatically link post title's to a URL.
	'auto_link' => true,

	// Categories go in this array. Format: 'name' => 'description'
	'categories' => array(

		'BlogPad' => 'A collection of posts related to "BlogPad".'
	),

	// Provide a path to the static posts directory here.
	'static_posts_dir' => 'content/static_posts',

	// Message to display on events of no posts to display.
	'no_post_message' => '<h1>Unfortunately, there is nothing to see here.</h1>',

	// How many posts do you want to be visible at any one time?
	'posts_per_page' => 5,

	// Specify 'DESC' if you want posts to appear from latest to old, or 'ASC' for the opposite.
	'post_sort_type' => 'desc',

	// Provide titles for different templates in this array. Format: 'pointer' => 'formatted title'. Read docs/structs/file_struct.md for assistance.
	'titles' => array(
		'HOMEPAGE' => 'Welcome to %blogname%',
		'POST' => '%posttitle% - %blogname%',
		'CATEGORY' => 'Posts in category %category%',
		'ERROR' => "You've errored. Oops"
	),

	// Login accounts go here. Format: 'username' => extended info array. Should contain first and last name, username + password (crypted)
	'accounts' => array(
		
		'admin' => array(
			'firstname' => 'BlogPad',
			'lastname' => 'Admin',
			'username' => 'admin',
			'password' => '$1$sF..FR0.$yzm..6pE9tgy.ryjHygOw0' /* password is "this-is-the-admin-password". Change it via "crypt_gen.php" */
		)
	)

);

?>
