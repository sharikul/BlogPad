<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $title; ?></title>
    <link rel="stylesheet" href="<?php echo $stylesheet; ?>">
    <link href='http://fonts.googleapis.com/css?family=Duru+Sans' rel='stylesheet' type='text/css'>
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
        <time datetime="<?php echo date('jS-F-Y', $post['date']); ?>">Posted on the <?php echo date('jS', $post['date']); ?> of <?php echo date('F, Y', $post['date']); ?></time>. 

        <time><strong>Updated on the <?php echo date('jS', $post['updated']); ?> of <?php echo date('F, Y', $post['updated']); ?></strong></time>
        <p><?php echo $post['excerpt']; ?></p>
    </article>
<?php endforeach; else: echo BlogPad::get_setting('no_post_message', 'Nothing to see here!'); endif; ?>

<div class="bp-pagination">
    <ul>
<?php if( !($pagenum - 1 <= 0) ): ?>        <li class="pagination"><a href="<?php echo Link_Parser::generate_link(BlogPad::$current_file, array("num" => $pagenum - 1, "word" => (isset($word)) ? $word: ""));?>"><?php echo (trim("Latest Posts") !== "") ? "Latest Posts": $pagenum - 1;?></a></li><?php endif;?>
<?php if($pagenum + 1 > 1 && $pagenum < $paginate["last_page"]): ?>        <li class="pagination"><a href="<?php echo Link_Parser::generate_link(BlogPad::$current_file, array("num" => $pagenum + 1, "word" => (isset($word)) ? $word: ""));?>"><?php echo (trim("Older Posts") !== "") ? "Older Posts": $pagenum + 1;?></a></li><?php endif;?>
    </ul>
</div>

    </div>
    <footer>Powered by BlogPad. Theme <strong>BlogPad</strong> by <a href="http://sharikul.comyr.com">Sharikul Islam</a>.</footer>
</body>
</html>