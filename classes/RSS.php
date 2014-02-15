<?php

class RSS extends BlogPad {

    protected static $xmlv = '1.0';

    protected static $enc = 'UTF-8';

    protected static $rssv = '2.0';

    /**
     * Gets the RSS feed set up.
     * @return void
     * 
     */ 

    public static function load() {
        header('Content-type: text/xml');
        spl_autoload_register('BlogPad::autoload');

        RSS::get_header();
        RSS::get_descriptions();
        RSS::get_posts();
        RSS::get_footer();
    }

    public static function setXMLV($version = '1.0') {
        return RSS::$xmlv = $version;
    }

    public static function setEnc($encoding = 'UTF-8') {
        return RSS::$enc = $encoding;
    }

    public static function setRSSV($version = '2.0') {
        return RSS::$rssv = $version;
    }

    private static function get_header() {
        printf("<?xml version=\"%s\" encoding=\"%s\"?>\n", RSS::$xmlv, RSS::$enc);
        printf("<rss version=\"%s\">\n", RSS::$rssv);
        echo "\t<channel>\n";
    }

    private static function get_descriptions() {
        printf("\t\t<title>%s</title>\n", BlogPad::get_setting('blogname', 'BlogPad Blog'));
        printf("\t\t<link>%s</link>\n", BlogPad::get_blog_homepage());
        printf("\t\t<description>%s</description>\n\n", BlogPad::get_setting('blogdescription', 'A BlogPad Blog'));
    }

    private static function get_posts() {

        if( BlogPad::has_setting('database') ) {
            Query::setup( BlogPad::get_setting('database') );
        }

        $have_posts = count( Post::get_all_posts() ) > 0;
        $paginate = false; 

        if( $have_posts ) {
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

        if( !$have_posts ) {
            echo "\t\t<item>\n";
            printf("\t\t\t<title>%s has no posts.</title>\n", BlogPad::get_setting('blogname', 'BlogPad Blog'));
            echo "\t\t</item>\n";
        }

        else {
            foreach( $posts as $post ) {
                echo "\t\t<item>\n";
                    printf("\t\t\t<title>%s</title>\n", $post['title']);
                    printf("\t\t\t<link>%s</link>\n", Link_Parser::generate_link('POST', array('slug' => $post['slug']) ));
                    printf("\t\t\t<guid>%s</guid>\n", Link_Parser::generate_link('POST', array('slug' => $post['slug']) ));
                    printf("\t\t\t<pubDate>%s</pubDate>\n", date('D, j F, Y G:i:s', $post['date']));
                    printf("\t\t\t<description>%s</description>\n", $post['description']);
                echo "\t\t</item>\n";
            }
        }
    }

    private static function get_footer() {
        echo "\t</channel>\n</rss>";
    }

}

?>