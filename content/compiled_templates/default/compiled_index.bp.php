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

	
<?php if( !empty($posts) ): foreach($posts as $post): ?>
		<h1><a href="<?php echo Link_Parser::generate_link('post', array('slug' => $post['slug']));?>"><?php echo $post['title'];?></a></h1>
		<p>Posted on <?php echo date('jS F Y', $post['date']); ?></p>
		<p><?php echo $post['excerpt']; ?></p>
<?php endforeach; else: echo 'Go away!'; endif; ?>

<div class="bp-pagination">
    
<?php if($pagenum - 1 !== 0): ?>    <p><a href="<?php echo Link_Parser::generate_link(BlogPad::$current_file, array("num" => $pagenum - 1));?>"><?php echo (trim("") !== "") ? "": $pagenum - 1;?></a></p><?php endif;?>
<?php if($pagenum + 1 <= $paginate["last_page"] + 1): ?>    <p><a href="<?php echo Link_Parser::generate_link(BlogPad::$current_file, array("num" => $pagenum + 1));?>"><?php echo (trim("Next page") !== "") ? "Next page": $pagenum + 1;?></a></p><?php endif;?>

</div>

<h3>From the footer!</h3>
</body>