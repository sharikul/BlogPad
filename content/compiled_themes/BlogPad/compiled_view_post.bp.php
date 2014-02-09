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

    <section id="comments">
            <div id="disqus_thread"></div>
    <script type="text/javascript">
        /* * * CONFIGURATION VARIABLES: EDIT BEFORE PASTING INTO YOUR WEBPAGE * * */
        var disqus_shortname = 'sharikulsblog'; // required: replace example with your forum shortname

        /* * * DON'T EDIT BELOW THIS LINE * * */
        (function() {
            var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
            dsq.src = '//' + disqus_shortname + '.disqus.com/embed.js';
            (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
        })();
    </script>
    <noscript>Please enable JavaScript to view the <a href="http://disqus.com/?ref_noscript">comments powered by Disqus.</a></noscript>
    <a href="http://disqus.com" class="dsq-brlink">comments powered by <span class="logo-disqus">Disqus</span></a>
    </section>
<?php else: echo BlogPad::get_setting('no_post_message', 'Nothing to see here!'); endif; ?>

    </div>
    <footer>Powered by BlogPad. &copy; 2014 <?php echo $metadata['blogname']; ?></footer>
</body>
</html>
