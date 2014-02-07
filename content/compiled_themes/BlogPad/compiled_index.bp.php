<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $title; ?></title>
    <link rel="stylesheet" href="<?php echo $stylesheet; ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
<?php if(BlogPad::$template === 'POST'): ?>
    <meta name="description" content="<?php echo $metadata['description']; ?>">
<?php else: ?>
    <meta name="description" content="<?php echo $metadata['blogdescription']; ?>">
<?php endif;?>

    <link rel="alternate" type="application/rss+xml" title="RSS" href="<?php echo $homepage; ?>/rss.php">
</head>
    <body>
        <header>
            <ul>
                <li>
                    <a href="<?php echo $homepage; ?>">Blog Home</a>
                </li>

                <li>
                    <a href="http://sharikul.comyr.com">My Website</a>
                </li>
            </ul>
        </header>

        <div id="content">

<?php if( !empty($posts) ): foreach($posts as $post): ?>
    <article>
        <h1><a href="<?php echo Link_Parser::generate_link('post', array('slug' => $post['slug']));?>"><?php echo $post['title'];?></a></h1>
        <p class="published">Published on the <time datetime="<?php echo date('jS-F-Y', $post['date']); ?>"><?php echo date('jS', $post['date']); ?> of <?php echo date('F, Y', $post['date']); ?> </time>by <a href="<?php echo Link_Parser::generate_link('profile', array('slug' => $post['author']));?>"><?php echo $post['author'];?></a>.</p>

        <p><?php echo $post['description']; ?></p>
    </article>
<?php endforeach; else: echo BlogPad::get_setting('no_post_message', 'Nothing to see here!'); endif; ?>

<div class="bp-pagination">
    <ul>
<?php if( !($pagenum - 1 <= 0) ): ?>        <li class="pagination"><a href="<?php echo Link_Parser::generate_link(BlogPad::$current_file, array("num" => $pagenum - 1, "word" => (isset($word)) ? $word: ""));?>"><?php echo (trim("Latest Posts") !== "") ? "Latest Posts": $pagenum - 1;?></a></li><?php endif;?>
<?php if($pagenum + 1 > 1 && $pagenum < $paginate["last_page"]): ?>        <li class="pagination"><a href="<?php echo Link_Parser::generate_link(BlogPad::$current_file, array("num" => $pagenum + 1, "word" => (isset($word)) ? $word: ""));?>"><?php echo (trim("Older Posts") !== "") ? "Older Posts": $pagenum + 1;?></a></li><?php endif;?>
    </ul>
</div>

    </div>
    <footer>Powered by BlogPad. &copy; 2014 <?php echo $metadata['blogname']; ?></footer>
</body>
</html>