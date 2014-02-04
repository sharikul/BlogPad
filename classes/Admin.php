<?php 
class Admin {

	static function load() {

        date_default_timezone_set( BlogPad::get_setting('timezone', 'Europe/London') );

	    spl_autoload_register('Admin::autoload');

        set_error_handler('BlogPad::throw_error');

	    session_start();

        if( !User::logged_in() ) {
            header('Location: '.BlogPad::get_blog_homepage().'/admin/login.php');
            exit;
        }

        // Sometimes the user's info can change whilst they're logged in. If that's the case, destroy the current session and redirect to login.
        if( !User::is_still_valid() ) {
            User::logout( BlogPad::get_blog_homepage().'/admin/login.php');
        }
        
        if( BlogPad::has_setting('database') ) {
		  Query::setup( BlogPad::get_setting('database') );
        }
		
		Admin::load_part('header');

		if( !isset( $_GET['page'] ) ) {

            $author = User::get_info('fullname');

			Admin::load_part('afterhead', array('title' => 'Add a Post'));
			Admin::load_part('posts/add_posts', array('page_type' => 'new'));
		}

		else {

			switch( trim( $_GET['page'] ) ) {

                case 'add_posts':
                    Admin::load_part('afterhead', array('title' => 'Add a Post'));
                    Admin::load_part('posts/add_posts', array('page_type' => 'new'));
                break;

                case 'edit_post':
                    Admin::load_part('afterhead', array('title' => 'Edit Post'));

                    if( !isset($_GET['post']) || trim($_GET['post']) === '' ) {
                        trigger_error('You cannot edit a post without supplying a `posts` parameter in the URL.', E_USER_ERROR);
                        exit;
                    }

                    $post = Post::get_post_by_slug( trim( $_GET['post'] ) );

                    $post = $post[0];

                    if( empty($post) ) {
                        trigger_error("Post <em>{$_GET['post']}</em> doesn't exist.", E_USER_ERROR);
                        exit;
                    }

                    if($post['author'] !== User::get_info('username') ) {
                        trigger_error('You cannot edit this post.', E_USER_ERROR);
                        exit;
                    }

                    $id = isset($post['id']) ? (int) $post['id']: 0;

                    $title = htmlentities($post['title'], ENT_QUOTES);

                    $categories = BlogPad::ser_to_list($post['categories']);

                    $description = htmlentities($post['description'], ENT_QUOTES);

                    $_post = htmlentities($post['post'], ENT_QUOTES);

                    $slug = $post['slug'];

                    $type = $post['type'];

                    $date = $post['date'];

                    $updated_date = $post['updated'];

                    $page_type = 'edit';

                    $author = trim($post['author']);

                    Admin::load_part('posts/add_posts', array(
                        'id' => $id,
                        'title' => $title,
                        'categories' => $categories,
                        'description' => $description,
                        'author' => $author,
                        'post' => $_post,
                        'date' => $date,
                        'slug' => $slug,
                        'page_type' => $page_type,
                        'p_type' => $type
                    ));
                break;

                case 'view_posts':

                    Admin::load_part('afterhead', array('title' => 'Your Posts'));

                    $message = '';

                    $posts = Post::get_posts_by( User::get_info('username') );

                    if( empty($posts) ) {
                        $message = 'You have not added any posts. Click the "Add a Post" button above!';
                    }

                    // Handle filters - filter by title
                    if( isset($_GET['filter_by'] ) && !empty($_GET['filter_by']) ) {
                        $filtered_posts = Post::filter($posts, 'title', trim($_GET['filter_by']));

                        if( empty($filtered_posts) ) {
                            $message = 'There are no posts whose title matches this filter. Click "remove filter" from above to renew your search.';
                        }

                        else {
                            $posts = $filtered_posts;
                        }

                    }

                    $current_page = ( isset($_GET['pagenum'] ) ) ? (int) $_GET['pagenum']: 1;

                    $paginate = BlogPad::paginate($posts, $current_page);

                    Admin::load_part('posts/view_posts', array(
                        'posts' => $paginate['set'], 
                        'show_next_page' => (bool) $paginate['has_next_page'],
                        'show_prev_page' => (bool) $paginate['has_prev_page'],
                        'have_posts' => !empty($posts),
                        'message' => $message,
                        'current_page' => $current_page
                    ));

                break;

			}
		}

        Admin::load_part('footer');
    }

	protected static function autoload($class) {
		include "../classes/$class.php";
	}

	protected static function load_part($part = null, $settings = array()) {
		if( is_null($part) ) {
			trigger_error('Please provide a part to load.', E_USER_ERROR);
			exit;
		}

		extract($settings);

		include "../content/includes/admin/$part.php";
	}

    /**
     * This method deals with creating, editing, and deleting posts, regardless of whether they're stored in the database or as a static file.
     * @return bool|array
     * 
     */ 

	protected static function handle_post($mode = 'db', $action = 'insert', array $options = array()) {

        // Return this array instead if there are any errors with the submission
        $errors = array();

        $id = ( array_key_exists('id', $options) ) ? (int) $options['id']: '';

        $title = ( array_key_exists('title', $options) ) ? $options['title']: '';

        $post = ( array_key_exists('post', $options) ) ? $options['post']: '';

        $description = ( array_key_exists('description', $options) ) ? $options['description']: '';

        $author = ( array_key_exists('author', $options) ) ? $options['author']: '';
        
        $slug = ( array_key_exists('slug', $options) ) ? $options['slug']: '';

        $categories = ( array_key_exists('categories', $options) ) ? trim( $options['categories'] ): '';

        $date = ( array_key_exists('date', $options) ) ? $options['date']: '';

        // This will be used later on to grab the slug of the current post.
        $get_by_id = Post::get_post_by_id($id);

        // Check that ALL the variables representing each post field have been completed.
        if( BlogPad::mempty($title, $post, $description, $slug) ) {
            $errors[] = 'To publish this post, you must complete all fields.';
        }

        // Check that the title of the post provided hasn't already been assigned to another post.
        if( Post::is_title($title) ) {

            // The form sent can be filled with updated data. Therefore if the Id of the current post and the matched post are the same, proceed with the update, else error.
            $get_by_title = Post::get_post_by_title( trim($title) );

            if( $get_by_title[0]['id'] !== $id ) {
                $errors[] = "The title <em>{$title}</em> has already been given to another post. Please provide another title.";
            }
        }

        // Check that the slug provided isn't in use
        if( Post::is_slug($slug) ) {

            // The form sent can be filled with updated data. Therefore if the Id of the current post and the matched post are the same, proceed with the update, else error.
            $get_slug = Post::get_post_by_slug( trim($slug) );

            if( $get_slug[0]['id'] !== $id ) {
                $errors[] = "The slug <em>{$slug}</em> is already being used by another post. Please provide a new slug.";
            }
        }

        if( empty($errors) ) {

            switch( $mode ) {

                case 'db':

                    // Halt everything if there's no way to insert into the database.
                    if( !Post::can_db_post() ) {
                        $errors[] = 'You cannot insert this post into the database.';
                        return $errors;
                    }

                    $operation = false;

                    switch( trim( $action ) ) {

                        case 'insert':
                            $operation = Query::_query('title, post, description, author, date, slug, categories', 'posts', array(
                            'action' => 'insert',

                            'values' => array(
                                ':title',
                                ':post',
                                ':description',
                                ':author',
                                ':date',
                                ':slug',
                                ':categories'
                            ),

                            'placeholders' => array(
                                ':title' => $title,
                                ':post' => $post,
                                ':description' => $description,
                                ':author' => User::get_info('username'),
                                ':date' => date('Y-m-d G:i:s'),
                                ':slug' => $slug,
                                ':categories' => BlogPad::list_to_ser($categories)
                            )

                            ));

                        break;

                        case 'update':
                            $operation = Query::_query('title, post, description, author, updated, slug, categories', 'posts', array(
                            'action' => 'update',
                            'update' => array(
                                'title' => $title,
                                'post' => $post,
                                'description' => $description,
                                'author' => User::get_info('username'),
                                'updated' => date('Y-m-d G:i:s'),
                                'slug' => $slug,
                                'categories' => BlogPad::list_to_ser($categories)
                            ),

                            // Update by slug in the database
                            'where' => 'slug = :slug',

                            'placeholders' => array(':slug' => $get_by_id['slug'])
                        ));

                        break;

                        case 'delete':
                            // Delete post by slug
                            $operation = Query::run('DELETE FROM posts WHERE slug = :slug', array(':slug' => $get_by_id['slug']));
                        break;

                        default:
                            trigger_error('Please supply an action to complete.', E_USER_ERROR);
                            exit;
                    }

                    return ( $operation ) ? true: false;

                break;

                case 'static':

                    // Halt everything if the post can't be statically generated.
                    if( !Post::can_static_post() ) {
                        $errors[] = 'You cannot save/create this post as a static post.';
                        return $errors;
                    } 

                    $static_posts_dir = BlogPad::static_posts_dir();

                    $template = '-- BEGIN METADATA
Title: %s

Date: %s

Author: %s

Categories: %s

Description: %s
-- END METADATA

%s';

                    switch( trim( $action ) ) {

                        case 'insert':
                            $filename = "$slug.bpp";
                        break;

                        case 'update':
                            $get_slug = Post::get_post_by_id($id);
                            $filename = "{$get_slug['slug']}.bpp";

                            $get_date = BP_Parser::parse_bpp("$static_posts_dir/$filename");

                            // The date retrieved from parsing is by default strtotime'd for us.
                            $date = date("Y-m-d G:i:s", $get_date['date']);

                            // If the slug of the submitted static post is different, rename the .bpp file
                            if( $slug !== $get_slug['slug'] ) {
                                rename("$static_posts_dir/$filename", "$static_posts_dir/$slug.bpp");
                                $filename = "$slug.bpp";
                            }

                        break;

                        case 'delete':
                            // Just to be on the safe side, retrieve the old slug that will be used towards deleting a static post.

                            $get_slug = Post::get_post_by_id($id);
                            
                            // Delete the file now
                            return unlink("$static_posts_dir/{$get_slug['slug']}.bpp");
                        break;
                    }

                if( trim( $action ) === 'insert' || trim($action) === 'update') {
                    $gen_template = sprintf($template, $title, $date, $author, $categories, $description, $post);

                    return file_put_contents("$static_posts_dir/$filename", $gen_template);
                }

            break;

            }
        }

        else {
            return $errors;
        }
	}
}

?>
