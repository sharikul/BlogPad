<?php

class Post extends BlogPad {

    static function can_static_post() {
        return ( !is_null(BlogPad::static_posts_dir()) && is_dir(BlogPad::static_posts_dir()) && is_writable(BlogPad::static_posts_dir()) );
    }

    static function can_db_post() {
        return !is_null( BlogPad::get_setting('database') ) && Query::connected() !== false;
    }

    static function is_static_post($slug = null) {
        if( is_null($slug) ) {
            trigger_error('Please provide a slug.', E_USER_ERROR);
            exit;
        }

        foreach (Post::get_static_posts() as $index => $post) {
            if( trim($post['slug']) === trim($slug) ) {
                return true;
            }
        }

        return false;
    }

    static function get_categories() {

        if( BlogPad::has_setting('categories') ) {

            $categories = array();

            foreach( BlogPad::get_setting('categories') as $category => $meta ) {
                $categories[] = $category;
            }

            return $categories;
        }

        return null;
    }

    /**
     * Returns the maximum posts per page, as defined in settings.php, else just the $default variable.
     * @return int
     * 
     */ 

    static function max_posts($default = 5) {
        return ( BlogPad::has_setting('posts_per_page') ) && is_numeric( BlogPad::get_setting('posts_per_page') ) ? (int) BlogPad::get_setting('posts_per_page'): $default;
    }

    /**
     * Enables the filtering of a results set. 
     * 
     * Method doesn't take arguments for flexibility.
     * @return array
     * 
     */ 

    static function filter() {

        $args = func_get_args();

        if( is_array($args[0]) ) {
            $posts = $args[0];

            $key = $args[1];
            $value = $args[2];

            $exactly = isset($args[3]) && is_bool($args[3]) ? $args[3]: false;
        }

        else {
            $posts = Post::get_all_posts();

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
                    $post['type'] = ( Post::is_static_post($post['slug']) ) ? 'static': 'db';
                    $filtered_set[] = $post;
                }
            }

            else {

                if( preg_match('/'.preg_quote($value).'/i', $post[$key]) ) {
                    $post['id'] = $index;
                    $post['type'] = ( Post::is_static_post($post['slug']) ) ? 'static': 'db';
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

        if( BlogPad::has_setting('static_posts_dir') ) {

            $dir = BlogPad::get_setting('base').'/'.BlogPad::get_setting('static_posts_dir');

            $opendir = opendir($dir);

            $posts = array();

            while( $static_post = readdir($opendir) ) {

                if( preg_match('/\.bpp$/', $static_post ) ) {

                    // Parsing the post will return an array by default.
                    $posts[ count( $posts ) ] = BP_Parser::parse_bpp( $dir.'/'.$static_post);

                    $posts[ count( $posts ) - 1 ] = array_map('stripslashes', $posts[ count( $posts ) - 1 ]); 

                    // The slug will be the name of the static post file. Remove the .bpp extension.
                    $posts[ count( $posts ) - 1]['slug'] = preg_replace('/\.bpp$/', '', $static_post);

                    // Serialize categories if they can be exploded by commas, else add the category to an array and then serialize.
                    $posts[ count( $posts ) - 1]['categories'] = BlogPad::list_to_ser($posts[ count( $posts ) - 1]['categories']);

                    // Remove html from the title and trim it as well.
                    $posts[ count( $posts ) - 1]['title'] = trim( strip_tags( $posts[ count( $posts ) - 1]['title'] ));

                    // Add an excerpt key
                    $posts[ count( $posts ) - 1]['excerpt'] = Parsedown::instance()->parse( substr($posts[ count( $posts ) - 1]['post'], 0, 200) ).( strlen($posts[ count( $posts ) - 1]['post']) > 200 ? '...': '');

                    // Add when the post was updated using filemtime
                    $posts[ count( $posts ) - 1]['updated'] = filemtime($dir.'/'.$static_post);

                    // Additional space before the author's name can pose problems, thus rid them here.
                    $posts[ count( $posts ) - 1]['author'] = trim( $posts[ count( $posts ) - 1]['author'] );

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

                $post = array_map('stripslashes', $post);

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
                        $posts[$index]['excerpt'] = Parsedown::instance()->parse( substr($posts[$index]['post'], 0, 200) ).( strlen($posts[$index]['post']) > 200 ? '...': '');
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

        $posts = $cache->get('posts');

        if( is_null($posts) ) {
            $posts = array_merge(Post::get_static_posts(), Post::get_db_posts());

            $cache->set('posts', $posts, 300);
        }

        else if( $posts !== array_merge(Post::get_static_posts(), Post::get_db_posts()) ) {
            $posts = array_merge(Post::get_static_posts(), Post::get_db_posts());
            
            $cache->set('posts', $posts, 300);
        }

        if( !empty( $posts ) ) {

            $dates = array();

            // $dates will be used later on to sort posts by ascending or descending order
            foreach( $posts as $post ) {
                $dates[] = $post['date'];
            }

            $sort = ( strtoupper( BlogPad::get_setting('post_sort_type', 'DESC') ) === 'DESC' ) ? SORT_DESC: SORT_ASC; 

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

        return Post::filter('author', $user, true);
    }

    static function get_post_by_title($title = null) {
        if( is_null($title) ) {
            trigger_error('Please provide a title to search for.', E_USER_ERROR);
            exit;
        }

        $post = Post::filter('title', trim($title), true);

        return ( !empty($post) ) ? $post: null;
    }

    static function get_post_by_id($id = 0) {

        $posts = Post::get_all_posts();

        if( isset($posts[$id]) ) {
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

        $post = Post::filter('slug', $slug);

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

        return ( Post::filter('title', trim($title), true) ) ? true: false;
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

        return ( Post::filter('slug', trim($slug), true) ) ? true: false;
    }

    static function get_title_of_slug($slug = null) {
        if( is_null($slug) ) {
            trigger_error('Please provide a slug to check.', E_USER_ERROR);
            exit;
        }

        if( Post::is_slug($slug) ) {
            $post = Post::get_post_by_slug($slug);

            return $post['title'];
        }
    }

}

?>
