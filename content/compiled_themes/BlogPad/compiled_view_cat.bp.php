<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $title; ?></title>
    <link rel="stylesheet" href="<?php echo $stylesheet; ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
<?php if(BlogPad::$template === 'POST'): ?>
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="<?php echo $metadata['title']; ?>">
    <meta name="twitter:description" content="<?php echo $metadata['description']; ?>">
    <meta name="description" content="<?php echo $metadata['description']; ?>">
<?php else: ?>
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="<?php echo $metadata['blogname']; ?>">
    <meta name="twitter:description" content="<?php echo $metadata['blogdescription']; ?>">
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

<div id="metadata">
    <h1>Posts in <?php echo $metadata['title']; ?></h1>
    <h3><?php echo $metadata['description']; ?></h3>
</div>
<hr>

<?php if( !empty($posts) ): foreach($posts as $post): ?>
    <article>
        <h1><a href="<?php echo Link_Parser::generate_link('post', array('slug' => $post['slug']));?>"><?php echo $post['title'];?></a></h1>
        <p id="body"><?php echo $post['description']; ?></p>

        <p class="inline">
            Posted in 
            <ul class="inline" id="categories">
<?php $catlist = BlogPad::ser_to_list($post["categories"]); foreach( (strpos($catlist, ",") ? explode(",", $catlist): array($catlist) ) as $category): ?>                <li class="categories"><a href="<?php echo Link_Parser::generate_link("category", array("word" => $category));?>"><?php echo $category;?></a></li><?php endforeach; ?>
            </ul>
        </p>
    </article>
<?php endforeach; else: echo BlogPad::get_setting('no_post_message', 'Nothing to see here!'); endif; ?>

<div class="bp-pagination">
    <ul>
<?php if( !($pagenum - 1 <= 0) ): ?>        <li class="pagination"><a href="<?php echo Link_Parser::generate_link(BlogPad::$template, array("num" => $pagenum - 1, "word" => (isset($word)) ? $word: ""));?>"><?php echo (trim("Latest Posts") !== "") ? "Latest Posts": $pagenum - 1;?></a></li><?php endif;?>
<?php if($pagenum + 1 > 1 && $pagenum < $paginate["last_page"]): ?>        <li class="pagination"><a href="<?php echo Link_Parser::generate_link(BlogPad::$template, array("num" => $pagenum + 1, "word" => (isset($word)) ? $word: ""));?>"><?php echo (trim("Older Posts") !== "") ? "Older Posts": $pagenum + 1;?></a></li><?php endif;?>
    </ul>
</div>

    </div>
    <footer>Powered by BlogPad. &copy; 2014 <?php echo $metadata['blogname']; ?></footer>
</body>
</html>