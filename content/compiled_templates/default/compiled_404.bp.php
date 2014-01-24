<?php $title = "Oops, you've made an error!"; ?>
<html>
	<head>
		<?php if(isset($title) ): ?>
			<title><?php echo $title; ?> &mdash; Sharikul's Blog</title>
		<?php else: ?>
			<title>BlogPad</title>
		<?php endif; ?>
		<link rel="stylesheet" href="<?php echo $stylesheet; ?>">

		<?php if(BlogPad::$current_file === 'POST'):?>
			<meta name="description" content="<?php echo $metadata['description']; ?>">
		<?php endif;?>
	</head>
	<body>
		<header>
			<ul>
				<li>Home</li>
				<li>Twitter</li>
				<li>Email</li>
			</ul>
		</header>
	<h1>If that was a post you were looking for, that doesn't exist &mdash; unfortunately! <a href="<?php echo $homepage; ?>">Go to the homepage?</a></h1>
<h3>From the footer!</h3>
</body>