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

<?php if( !empty($post) ):  ?>

	<h1><a href="<?php echo Link_Parser::generate_link('post', array('slug' => $post['slug']));?>"><?php echo $post['title'];?></a></h1>
    <h3><?php echo $post['description']; ?></h3>

	<time datetime="<?php echo date('jS-F-Y', $post['date']); ?>">Posted on <?php echo date('jS F, Y', $post['date']); ?></time>

	<?php echo Parsedown::instance()->parse($post['post']);?>

    <p>Post updated on <?php echo date('jS \of F, Y', $post['updated']); ?></p>

    <ul>
<?php $catlist = BlogPad::ser_to_list($post["categories"]); foreach( explode(",", $catlist) as $category): ?><a href="<?php echo Link_Parser::generate_link("category", array("word" => $category));?>">        <li class="cat"><?php echo $category;?></li></a><?php endforeach; ?>
    </ul>


<?php else: echo 'Go away!'; endif; ?>

<h3>From the footer!</h3>
</body>