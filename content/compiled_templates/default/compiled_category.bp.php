<?php $title = "Results for $category"; ?>
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

<header style="background-color: lavender">
	<h1 style="padding: 15px; font-family: sans-serif;text-align: center;"><?php echo $metadata['title']; ?></h1>
	<h3 style="font-family: sans-serif;text-align: center;color:lightblue;margin: 1px"><?php echo $metadata['description']; ?></h1>
</header>
	
	<?php if( !empty($posts) ): foreach($posts as $post): ?>
		<article>
			<h1><a href="<?php echo Link_Parser::generate_link('post', array('slug' => $post['slug']));?>"><?php echo $post['title'];?></a></h1>
			<span style="color: gray;">Posted on <em><?php echo date('jS F, Y', $post['date']); ?></em></span>
			<h4><?php echo $post['excerpt']; ?></h1>
		</article>
		<br>
	<?php endforeach; else: echo BlogPad::get_setting('no_post_message', 'Nothing to see here!'); endif; ?>

	<div class="bp-pagination">

		<div class="bp-countdown"><?php $i = 1; while($i <= $paginate["last_page"]): ?>

			<li><a href="<?php echo Link_Parser::generate_link(BlogPad::$current_file, array("num" => $i, "word" => (isset($word)) ? $word: ""));?>"><?php echo $i;?></a></li>
			
		<?php $i++; endwhile; ?></div>

	</div>

<h3>From the footer!</h3>
</body>