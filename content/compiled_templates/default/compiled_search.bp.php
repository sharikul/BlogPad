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
<h1>Search results for "<?php echo $query; ?>"</h1>

<?php if( !empty($posts) ): foreach($posts as $post): ?>
    <h1><a href="<?php echo Link_Parser::generate_link('post', array('slug' => $post['slug']));?>"><?php echo $post['title'];?></a></h1>
    <p>Posted on: <?php echo date('F, Y', $post['date']); ?></p>
<?php endforeach; else: echo BlogPad::get_setting('no_post_message', 'Nothing to see here!'); endif; ?>

<div class="bp-pagination">
<?php if( !($pagenum - 1 <= 0) ): ?>    <li class="cat"><a href="<?php echo Link_Parser::generate_link(BlogPad::$current_file, array("num" => $pagenum - 1, "word" => (isset($word)) ? $word: ""));?>"><?php echo (trim("Previous") !== "") ? "Previous": $pagenum - 1;?></a></li><?php endif;?>
<?php if($pagenum + 1 > 1 && $pagenum < $paginate["last_page"]): ?>    <li class="cat"><a href="<?php echo Link_Parser::generate_link(BlogPad::$current_file, array("num" => $pagenum + 1, "word" => (isset($word)) ? $word: ""));?>"><?php echo (trim("Next") !== "") ? "Next": $pagenum + 1;?></a></li><?php endif;?>
</div>