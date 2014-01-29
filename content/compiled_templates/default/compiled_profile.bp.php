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

		<form id="form" method="POST">
			<input type="text" name="s" id="s">
		</form>

		<script>
			document.getElementById('s').onkeyup = function() {
				document.getElementById('form').setAttribute('action', '<?php echo $homepage; ?>/search/' + this.value + '/');
			}
		</script>

<h1><?php echo $username; ?></h1>

<article>
    <?php if( !empty($posts) ): foreach($posts as $post): ?>
        <h1><a href="<?php echo Link_Parser::generate_link('post', array('slug' => $post['slug']));?>"><?php echo $post['title'];?></a></h1>
    <?php endforeach; else: echo BlogPad::get_setting('no_post_message', 'Nothing to see here!'); endif; ?>
</article>

<div class="bp-pagination">

    <div>
<?php if( !($pagenum - 1 <= 0) ): ?>        <a href="<?php echo Link_Parser::generate_link(BlogPad::$current_file, array("num" => $pagenum - 1, "word" => (isset($word)) ? $word: ""));?>"><?php echo (trim("") !== "") ? "": $pagenum - 1;?></a><?php endif;?>
<?php if($pagenum + 1 > 1 && $pagenum < $paginate["last_page"]): ?>        <a href="<?php echo Link_Parser::generate_link(BlogPad::$current_file, array("num" => $pagenum + 1, "word" => (isset($word)) ? $word: ""));?>"><?php echo (trim("Next page") !== "") ? "Next page": $pagenum + 1;?></a><?php endif;?>
    </div>

</div>