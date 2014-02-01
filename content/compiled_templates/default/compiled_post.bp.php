<html>
	<head>
		<title><?php echo $title; ?></title>
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

		<form id="form" method="POST">
			<input type="text" name="s" id="s">
		</form>

		<script>
			document.getElementById('s').onkeyup = function() {
				document.getElementById('form').setAttribute('action', '<?php echo $homepage; ?>/search/' + this.value + '/');
			}
		</script>

<?php if( !empty($post) ):  ?>

	<h1><a href="<?php echo Link_Parser::generate_link('post', array('slug' => $post['slug']));?>"><?php echo $post['title'];?></a></h1>
    <p>Posted by <?php echo $post['author']; ?></p>
    <h3><?php echo $post['description']; ?></h3>

	<time datetime="<?php echo date('jS-F-Y', $post['date']); ?>">Posted on <?php echo date('jS F, Y', $post['date']); ?></time>

	<?php echo Parsedown::instance()->parse($post['post']);?>

    <p>Post updated on <?php echo date('jS \of F, Y', $post['updated']); ?></p>

    <ul>
<?php $catlist = BlogPad::ser_to_list($post["categories"]); foreach( (strpos($catlist, ",") ? explode(",", $catlist): array($catlist) ) as $category): ?><a href="<?php echo Link_Parser::generate_link("category", array("word" => $category));?>">        <li class="cat"><?php echo $category;?></li></a><?php endforeach; ?>
    </ul>


<?php else: echo BlogPad::get_setting('no_post_message', 'Nothing to see here!'); endif; ?>

<h3>From the footer!</h3>
</body>