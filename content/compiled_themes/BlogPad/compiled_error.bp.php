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

<h1>Damn. You've errored. You may wanna go back to the homepage now.</h1>

    </div>
    <footer>Powered by BlogPad. &copy; 2014 <?php echo $metadata['blogname']; ?></footer>
</body>
</html>