<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $title; ?></title>
    <link rel="stylesheet" href="<?php echo $stylesheet; ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
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

<?php if( !empty($post) ):  ?>
    <article>
        <h1><a href="<?php echo Link_Parser::generate_link('post', array('slug' => $post['slug']));?>"><?php echo $post['title'];?></a></h1>
        <h3><?php echo $post['description']; ?></h3>
        <p class="published">Published on the <time datetime="<?php echo date('jS-F-Y', $post['date']); ?>"><?php echo date('jS', $post['date']); ?> of <?php echo date('F, Y', $post['date']); ?> </time>by <a href="<?php echo Link_Parser::generate_link('profile', array('slug' => $post['author']));?>"><?php echo $post['author'];?></a>.</p> 
        <hr>
        
        <div id="body">
            <?php echo Parsedown::instance()->parse($post['post']);?>
        </div>

        <br>
        <p class="inline">Categories: 
            <ul class="inline" padding="5px" id="categories">
<?php $catlist = BlogPad::ser_to_list($post["categories"]); foreach( (strpos($catlist, ",") ? explode(",", $catlist): array($catlist) ) as $category): ?>                <li class="categories"><a href="<?php echo Link_Parser::generate_link("category", array("word" => $category));?>"><?php echo $category;?></a></li><?php endforeach; ?>
            </ul>
        </p>
    </article>
<?php else: echo BlogPad::get_setting('no_post_message', 'Nothing to see here!'); endif; ?>

    </div>
    <footer>Powered by BlogPad. Theme <strong>BlogPad</strong> by <a href="http://sharikul.comyr.com">Sharikul Islam</a>.</footer>
</body>
</html>
