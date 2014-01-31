<?php

class Post extends BlogPad {

    static function can_static_post() {
        return ( !is_null(self::static_posts_dir()) );
    }

    static function can_db_post() {
        return !is_null( self::get_setting('database') ) && Query::connected() !== false;
    }

    static function is_static_post($slug = null) {
        if( is_null($slug) ) {
            trigger_error('Please provide a slug.', E_USER_ERROR);
            exit;
        }

        return !empty( self::filter( self::get_static_posts(), 'slug', trim($slug) ) );
    }

    static function get_categories() {

        if( self::has_setting('categories') ) {

            $categories = array();

            foreach( self::get_setting('categories') as $category => $meta ) {
                $categories[] = $category;
            }

            return $categories;
        }

        return null;
    }

    /**
     * Returns the maximum posts per page, as defined in blog.php, else just the $default variable.
     * @return int
     * 
     */ 

    static function max_posts($default = 5) {
        return ( self::has_setting('posts_per_page') ) && is_numeric( self::get_setting('posts_per_page') ) ? (int) self::get_setting('posts_per_page'): $default;
    }

    static function filter() {

        $args = func_get_args();

        if( is_array($args[0]) ) {
            $posts = $args[0];

            $key = $args[1];
            $value = $args[2];

            $exactly = isset($args[3]) && is_bool($args[3]) ? $args[3]: false;
        }

        else {
            $posts = self::get_all_posts();

            $key = $args[0];
            $value = $args[1];

            $exactly = isset($args[2]) && is_bool($args[2]) ? $args[2]: false; 
        }

        $filtered_set = array();

        // Loop through the $posts array, and store filtered matches to $filtered_set
        foreach( $posts as $index => $post ) {

            // Just make sure that the key even exists before continuing
            if( !array_key_exists($key, $post) ) {
                trigger_error("Key <em>{$key}</em> not found.", E_USER_ERROR);
                exit;
            }

            // switching 'exactly' on will result in stricter searches.
            if( $exactly ) {

                if($post[$key] === $value) {
                    $post['id'] = $index;
                    $post['type'] = ( self::is_static_post($post['slug']) ) ? 'static': 'db';
                    $filtered_set[] = $post;
                }
            }

            else {

                if( preg_match('/'.preg_quote($value).'/i', $post[$key]) ) {
                    $post['id'] = $index;
                    $post['type'] = ( self::is_static_post($post['slug']) ) ? 'static': 'db';
                    $filtered_set[] = $post;
                }
            }
        }

        return $filtered_set;
    }

    /**
     * Returns an array with the content of static posts
     * @return array|null
     * 
     */

    static function get_static_posts() {

        if( self::has_setting('static_posts_dir') ) {

            $dir = self::get_setting('base').'/'.self::get_setting('static_posts_dir');

            $opendir = opendir($dir);

            $posts = array();

            while( $static_post = readdir($opendir) ) {

                if( preg_match('/\.bpp$/', $static_post ) ) {

                    // Parsing the post will return an array by default.
                    $posts[ count( $posts ) ] = BP_Parser::parse_bpp( $dir.'/'.$static_post);
                    
                    $post = $posts[ count( $posts ) - 1];

                    // The slug will be the name of the static post file. Remove the .bpp extension.
                    $post['slug'] = preg_replace('/\.bpp$/', '', $static_post);

                    // Serialize categories if they can be exploded by commas, else add the category to an array and then serialize.
                    $post['categories'] = self::list_to_ser($post['categories']);

                    // Remove html from the title and trim it as well.
                    $post['title'] = trim( strip_tags( $post['title'] ));

                    // Add an excerpt key
                    $post['excerpt'] = Parsedown::instance()->parse( substr($post['post'], 0, 200) );

                    // Add when the post was updated using filemtime
                    $post['updated'] = filemtime($dir.'/'.$static_post);

                    // Additional space before the author's name can pose problems, thus rid them here.
                    $post['author'] = trim( $post['author'] );

                }   
            }

            return ( !empty( $posts ) ) ? $posts: null;
        }

    }

    /**
     * Returns an array with content of posts from the database.
     * @return array|null
     * 
     */ 

    static function get_db_posts() {

        $posts = array();

        $get_posts = Query::run('SELECT * FROM posts');

        if( $get_posts ) {

            // MYSQL queries return associative and numerical keys, so let's get rid of them here.
            foreach( $get_posts as $index => $post ) {

                // Loop through the nested array
                foreach( $post as $column => $value) {
                    if( !is_numeric( $column ) ) {
                        $posts[$index][$column] = $value;
                    }

                    // strtotime the date!
                    if( $column === 'date' ) {
                        $posts[$index][$column] = strtotime($value);
                    }

                    // strtotime the updated date!
                    if( $column === 'updated' ) {
                        $posts[$index][$column] = strtotime($value);
                    }

                    // Remove html from the title
                    if( $column === 'title' ) {
                        $posts[$index][$column] = strip_tags($value);
                    }

                    // Add excerpt key
                    if( isset($posts[$index]['post'] ) ) {
                        $posts[$index]['excerpt'] = Parsedown::instance()->parse( substr($posts[$index]['post'], 0, 200) );
                    }
                    
                }
            }

        }

        return ( !empty($posts) ) ? $posts: null;
    }

    /**
     * Returns an array containing static and 'dynamic' posts from the database.
     * @return array|null
     * 
     */ 

    static function get_all_posts() {

        // Cache the cache call!
        $cache = Cache::init();

        $static_posts = $cache->get('static_posts');

        $db_posts = $cache->get('db_posts');

        if( is_null($static_posts) ) {
            $static_posts = self::get_static_posts();

            // Store data in the cache for 5 minutes
            $cache->set('static_posts', serialize($static_posts), 300);
        }

        else if( is_null($db_posts) ) {
            $db_posts = self::get_db_posts();

            $cache->set('db_posts', serialize($db_posts), 300);
        }

        else {

            if( $static_posts !== serialize(self::get_static_posts() )) {
                $static_posts = self::get_static_posts();

                // Recache new array
                $cache->set('static_posts', serialize($static_posts), 300);
            }

            else if( $db_posts !== serialize(self::get_db_posts() )) {
                $db_posts = self::get_db_posts();

                $cache->set('db_posts', serialize($db_posts), 300);
            }
        }

        $static_posts = ( !is_array($static_posts) ) ? unserialize($static_posts): $static_posts;
        $db_posts = ( !is_array($db_posts) ) ? unserialize($db_posts): $db_posts;

        $posts = array();

        if( !empty( $static_posts ) && !empty( $db_posts ) ) {
            $posts = array_merge( $static_posts, $db_posts );
        }

        else {

            if( !empty( $static_posts ) ) {
                $posts = array_merge($static_posts, $posts);
            }

            else if( !empty( $db_posts) ) {
                $posts = array_merge($db_posts, $posts);
            }
        }

        if( !empty( $posts ) ) {

            $dates = array();

            // $dates will be used later on to sort posts by ascending or descending order
            foreach( $posts as $post ) {
                $dates[] = $post['date'];
            }

            $sort = ( strtoupper( self::get_setting('post_sort_type', 'DESC') ) === 'DESC' ) ? SORT_DESC: SORT_ASC; 

            // Sort by date now.
            array_multisort($dates, $sort, $posts);

            return ( !empty($posts) ) ? $posts: null;
        }

        else {
            return null;
        }
    }

    static function get_posts_by($user = null) {
        if( is_null($user) ) {
            trigger_error('Please provide a user.', E_USER_ERROR);
            exit;
        }

        return self::filter('author', $user, true);
    }

    static function get_post_by_title($title = null) {
        if( is_null($title) ) {
            trigger_error('Please provide a title to search for.', E_USER_ERROR);
            exit;
        }

        $post = self::filter('title', trim($title), true);

        return ( !empty($post) ) ? $post: null;
    }

    static function get_post_by_id($id = 0) {

        $posts = self::get_all_posts();

        if( isset($posts[$id] ) ) {
            $posts[$id]['id'] = $id;
            return $posts[$id];
        }

        return false;
    }

    static function get_post_by_slug($slug = null) {
        if( is_null($slug) ) {
            trigger_error('Please provide a slug.', E_USER_ERROR);
            exit;
        }

        $slug = trim($slug);

        $post = self::filter('slug', $slug);

        return ( !empty($post) ) ? $post: null;
    }

    /**
     * Checks whether the provided title has already been assigned to another post.
     * @return bool
     * 
     */ 

    static function is_title($title = null) {
        if( is_null($title) ) {
            trigger_error('Please provide a title to check.', E_USER_ERROR);
            exit;
        }

        return ( self::filter('title', trim($title), true) ) ? true: false;
    }

    /**
     * Checks whether the provided slug belongs to another post.
     * @return bool
     * 
     */ 

    static function is_slug($slug = null) {
        if( is_null($slug) ) {
            trigger_error('Please provide a slug to check.', E_USER_ERROR);
            exit;
        }

        return ( self::filter('slug', trim($slug), true) ) ? true: false;
    }

    static function get_title_of_slug($slug = null) {
        if( is_null($slug) ) {
            trigger_error('Please provide a slug to check.', E_USER_ERROR);
            exit;
        }

        if( self::is_slug($slug) ) {
            $post = self::get_post_by_slug($slug);

            return $post['title'];
        }
    }

}

?>
