<?php

include 'classes/BlogPad.php';
include 'classes/Query.php';
include 'classes/User.php';
include 'classes/BP_Parser.php';
include 'classes/Admin.php';
include 'classes/Parsedown.php';
include 'classes/Link_Parser.php';
include 'classes/Cache.php';
include 'classes/Post.php';

if( BlogPad::has_setting('database') ) {
    Query::setup(BlogPad::get_setting('database'));
}

$has_posts = count( Post::get_all_posts() ) > 0;
$paginate = false;

if( $has_posts ) {

    $posts = Post::get_all_posts();

    if( isset($_REQUEST['pagenum']) ) {
        $paginate = true;
        $pagenum = (int) $_REQUEST['pagenum'];
    }

    if( isset($_REQUEST['user']) ) {
        $posts = Post::get_posts_by( $_REQUEST['user'] );
    }

    $posts = ( $paginate ) ? BlogPad::paginate( $posts, $pagenum ): $posts;
    $posts = isset($posts['set']) ? $posts['set']: $posts;

    if( empty($posts) ) {
        $have_posts = false;
    }
}

header('Content-type: text/xml');
?>
<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<rss version="2.0">
    <channel>
        <title><?php echo BlogPad::get_setting('blogname', 'BlogPad Blog');?></title>
        <link><?php echo BlogPad::get_blog_homepage();?></link>
        <description><?php echo BlogPad::get_setting('blogdescription', 'A BlogPad Blog');?></description>

    <?php if( !$has_posts ): ?>
        <item>
            <title><?php echo BlogPad::get_setting('blogname', 'BlogPad Blog');?> has no posts.</title>
        </item>
    <?php else: foreach($posts as $post): ?>
        <item>
            <title><?php echo $post['title'];?></title>
            <link><?php echo Link_Parser::generate_link('POST', array('slug' => $post['slug']));?></link>
            <guid><?php echo Link_Parser::generate_link('POST', array('slug' => $post['slug']));?></guid>
            <pubDate><?php echo date('D, j F, Y G:i:s', $post['date']);?></pubDate>
            <description><?php echo $post['description'];?></description>
        </item>
    <?php endforeach; endif; ?>
    </channel>
</rss>