<?php

class BlogPad {

	static $vars = array();

	static $to_load = '';

	static $current_file = '';

	static function load() {

		set_error_handler('self::throw_error');

		spl_autoload_register('self::autoload');

		// Halt the execution of BlogPad if there's no possibly way of dealing with posts.
		if( !self::should_init() ) {
			trigger_error('BlogPad cannot execute as you have not specified a way to store posts (e.g. dynamically through the database or statically through files).', E_USER_ERROR);
			exit;
		}	

		if( self::has_setting('database') ) {

			// Get the database set up
			Query::setup( self::get_setting('database') );

		}

		if( !self::has_setting('base') ) {
			trigger_error('The base path must be specified in settings.php.', E_USER_ERROR);
			exit;
		}

		$folder = self::get_theme_dir();

		if( !is_dir($folder) ) {
			trigger_error("BlogPad cannot locate the folder for theme '$using' in content/themes.", E_USER_ERROR);
			exit;
		}

		if( !is_file("$folder/struct.bpd") ) {
			trigger_error("BlogPad cannot load theme '$using' as it couldn't find struct.bpd in content/themes/$using/.", E_USER_ERROR);
			exit;
		}

		$structure = self::get_file_struct();

		if( !isset($structure['paths']['URL_STRUCT']) ) {
			trigger_error("URL definition file has not been defined in $folder/struct.bpd.", E_USER_ERROR);
			exit;
		}

		if( empty($structure) ) {
			trigger_error("BlogPad couldn't find structures in $folder/struct.bpd.", E_USER_ERROR);
			exit;
		}

		if( empty($structure['paths']) ) {
			trigger_error("BlogPad couldn't find any file structures defined in $folder/struct.bpd.", E_USER_ERROR);
			exit;
		}

	
		if( !isset($structure['paths']['ERROR']) ) {
			trigger_error("You haven't delegated an error page in $folder/struct.bpd.", E_USER_ERROR);
			exit;
		}

		$pointers = self::get_pointers();

		// Link_Parser will populate self::$vars as well as insert a pointer in self::$to_load
		Link_Parser::load();

		if( !empty(self::$to_load) ) {

			self::$current_file = self::$to_load;
			
			self::load_page($pointers[self::$to_load], self::$vars); 
		}

		else {
			self::$current_file = 'HOMEPAGE';
			self::load_page($pointers['HOMEPAGE'], self::$vars);
		}
	}

	static function list_to_ser($list = null) {
        if( is_null($list) ) {
            trigger_error('Please provide a string to process.', E_USER_ERROR);
            exit;
        }

        if( strpos($list, ',') ) {
            $parts = explode(',', $list);

            $parts = array_map('trim', $parts);

            $processed_parts = array();

            foreach( $parts as $part ) {
            	if( trim($part) !== '' ) {
            		$processed_parts[] = $part;
            	}
            }

            return serialize($processed_parts);
        }

        else {
            return serialize( array( trim( $list ) ) );
        }
    }

    static function ser_to_list($ser = null) {
    	if( is_null($ser) ) {
    		trigger_error('Please provide a serialized string to process.', E_USER_ERROR);
    		exit;
    	}

    	$conv = unserialize($ser);

    	return ( count($conv) === 1 ) ? $conv[0]: implode(', ', $conv);
    }

	static function get_theme_name() {
		return self::get_setting('using');
	}

	static function get_templates_dir() {
		return self::get_setting('base').'/content/compiled_templates';
	}

	static function get_theme_dir() {
		return self::get_setting('base').'/content/themes/'.self::get_setting('using');
	}

	static function get_blog_homepage() {
		return 'http://'.rtrim($_SERVER['HTTP_HOST'].'/'.basename( self::get_setting('base') ), '/');
	}

	static function get_file_struct() {
		return BP_Parser::parse_bpd( self::get_theme_dir().'/struct.bpd');
	}

	static function get_url_struct($raw = false) {
		$struct = self::get_file_struct();

		return BP_Parser::parse_bpd( self::get_theme_dir().'/'.$struct['paths']['URL_STRUCT'], $raw);
	}

	static function get_pointers() {
		$structure = self::get_file_struct();

		$folder = self::get_theme_dir();

		$pointers = array();

		foreach( $structure['paths'] as $type => $path ) {

			// Compile those templates with a .bp extension.
			if( preg_match('/\.bp\b$/', $path) ) {
				$pointers[$type] = BP_Parser::gen("$folder/$path");
			}

			// Keep those templates without a .bp extension in their original form.
			else {

				if( $type === 'STYLESHEET' ) {
					$pointers[$type] = self::get_blog_homepage().'/content/themes/'.self::get_theme_name()."/$path";
				}

				else {
					$pointers[$type] = "$folder/$path";
				}
			}
		}

		return $pointers;
	}

	static function load_page($page = null, array $params = array() ) {
		if( is_null($page) ) {
			trigger_error('Please provide a page to load.', E_USER_ERROR);
			exit;
		}

		extract($params);

		$pointers = self::get_pointers();

		$stylesheet = $pointers['STYLESHEET'];

		$settings = self::get_blog_settings();

		$homepage = self::get_blog_homepage();

		$titles = isset($settings['titles']) ? $settings['titles']: '';

		$includes = "$homepage/content/includes";

		$js_includes = $includes.'/js/';
		$css_includes = $includes.'/css/';

		switch( self::$current_file ) {
			case 'CATEGORY':

				$posts = array();

				$metadata = array();

				$category = $word;

				if( !array_key_exists($category, $settings['categories']) ) {
					self::four_o_four();
				}

				$metadata['title'] = trim($category);
				$metadata['description'] = $settings['categories'][$category];

				foreach( Post::get_all_posts() as $post ) {

					// Trim the categories to boost search reliability.
					$_categories = array_map('trim', unserialize($post['categories']));

					if( in_array($category, $_categories) ) {
						$posts[ count($posts) ] = $post;
					}
				}

				if( !isset( $pagenum ) || empty($pagenum) ) {
					$pagenum = 1;
				}

				$paginate = self::paginate($posts, $pagenum);

				$posts = $paginate['set'];

				$title = ( !empty($titles) ) ? self::bpf( $titles['CATEGORY'], array('category' => $category)): "Posts in $category";

				include $pointers['CATEGORY'];

			break;

			case 'POST':

				$post = Post::filter('slug', $_post, true);

				if( empty( $post ) ) {
					self::four_o_four();
				}

				$metadata = array();

				$post = $post[0];

				$title = ( !empty($titles) ) ? self::bpf( $titles['POST'], array('posttitle' => $post['title']) ): $post['title'];

				$metadata['title'] = $post['title'];
				$metadata['description'] = $post['description'];

				include $pointers['POST'];

			break;

			case 'HOMEPAGE':

				if( !isset($pagenum) ) {
					$pagenum = 1;
				}

				$paginate = self::paginate(Post::get_all_posts(), $pagenum);

				$posts = $paginate['set'];

				$title = ( !empty($titles) ) ? self::bpf($titles['HOMEPAGE']): self::get_setting('blogname');

				include $pointers['HOMEPAGE'];

			break;

			case 'PROFILE':

				if( $pagenum === '' ) {
					$pagenum = 1;
				}

				$paginate = self::paginate(Post::get_posts_by($username), $pagenum);

				$posts = $paginate['set'];

				if( empty($posts) ) {
					self::four_o_four();
				}

				// for pagination
				$word = $username;

				$title = ( !empty($titles) ) ? self::bpf( $titles['PROFILE'], array('username' => $username) ): "$username's Profile";

				include $pointers['PROFILE'];

			break;

			case 'SEARCH':

				$query = urldecode($query);

				// for pagination
				$word = $query;

				if( $pagenum === '' ) {
					$pagenum = 1;
				}

				$paginate = self::paginate( Post::filter('title', $query), $pagenum );

				$posts = $paginate['set'];

				$title = ( !empty($titles) ) ? self::bpf( $titles['SEARCH'], array('searchquery' => $query) ): "Search results for '$query'";

				include $pointers['SEARCH'];
			break;

			default:

				$title = ( !empty($titles) ) ? self::bpf( $titles['HOMEPAGE'] ): self::get_setting('blogname');
				
				include $pointers['HOMEPAGE'];
			break;

		}
		
	}

	static function get_array_from_file($file = null) {
		if( is_null($file) ) {
			trigger_error('Please provide a file to extract data from before continuing.', E_USER_ERROR);
			exit;
		}

		if( !is_file($file) ) {
			trigger_error("Couldn't find $file.", E_USER_ERROR);
			exit;
		}

		if( !is_readable($file) ) {
			trigger_error("Couldn't read contents of $file.", E_USER_ERROR);
			exit;
		}

		ob_start();

		$content = include $file;

		echo serialize($content);

		$data = ob_get_contents();

		ob_end_clean();

		if( is_array(unserialize($data) ) ) {
			return unserialize($data);
		} 

		else {
			trigger_error("Couldn't find an array in $file.", E_USER_ERROR);
			exit;
		}
	}

	private static function autoload($class) {
		include "./classes/$class.php";
	}

	static function get_blog_settings() {
		return self::get_array_from_file(dirname(__DIR__).'/settings.php');
	}

	/**
	 * Processes a posts array and returns it after sectioning data for pagination purposes, as well as other data.
	 * 
	 * @return array
	 * 
	 */

	static function paginate(array $posts = array(), $current_page = 1) {

		if( $current_page <= 0 ) {
			$current_page = 1;
		}

		$max_posts = Post::max_posts();

		$last_page = ceil( count($posts) / $max_posts );

		if( $current_page > $last_page ) {
			$current_page = $last_page;
		}

		$starting_point = ( $current_page - 1 ) * $max_posts;

		return array(
			'set' => array_slice($posts, $starting_point, $max_posts),
			'has_next_page' => !( $current_page >= $last_page ),
			'has_prev_page' => $last_page > 1 && $current_page <= (int) $last_page,
			'last_page' => $last_page
		);
	}

	static function throw_error( $number, $message, $file, $line ) {

		switch( $number ) {
			case 8:
				$message = 'Couldn\'t find variable: '.preg_replace('/Undefined (variable|index)\: /', '', $message);
			break;
		}

		echo "<h1 style='font-family: sans-serif !important; padding: 5em;'>$message -> $line in $file</h1>";
	}

	static function has_setting($setting = null, $isval = null) {
		if( is_null($setting) ) {
			trigger_error('Please provide a setting to check.', E_USER_ERROR);
			exit;
		}

		$settings = self::get_blog_settings();

		return ( !is_null($isval) ) ? isset($settings[$setting]) && !empty($settings[$setting]) && $settings[$setting] === $isval: isset($settings[$setting]) && !empty($settings[$setting]);
	}

	static function get_setting($setting = null, $instead = null) {
		if( is_null($setting) ) {
			trigger_error('Please provide a setting to retrieve.', E_USER_ERROR);
			exit;
		}

		$settings = self::get_blog_settings();

		if( self::has_setting($setting) ) {
			return $settings[$setting];
		}

		else {
			return ( !is_null($instead) ) ? $instead: null;
		}
	}

	static function four_o_four($message = 'Sorry, you have made an error.', $show_error = true, array $params = array() ) {

		$pointers = self::get_pointers();

		$stylesheet = $pointers['STYLESHEET'];

		// Store the path to the homepage in a variable so that it can referenced in an error template.
		extract(array('homepage' => self::get_blog_homepage()) );

		// Turn array keys into variables so that global vars such as stylesheet stay in scope even on error.
		extract($params);

		// Just incase the message is wiped out, reset the message.
		if( empty($message) ) {
			$message = 'Sorry, you have made an error.';
		}

		// Some pages might not require an include of the error template, thus only show the page on true.
		if( $show_error ) {
			include $pointers['ERROR'];
			exit;
		}

		else {

			if( self::$current_file !== 'HOMEPAGE' ) {
				$message .= ' <a href="'.self::get_blog_homepage().'">Go back to the homepage?</a>';
			}

			trigger_error($message);
		}
	}

	static function static_posts_dir() {
		return ( self::has_setting('static_posts_dir') ) ? self::get_setting('base').'/'.self::get_setting('static_posts_dir'): null;
	}

	/**
	 * Checks whether all variables provided are not empty.
	 * @return bool
	 * 
	 * @see http://stackoverflow.com/a/7798842
	 * 
	 */ 

	static function mempty() {
	    foreach(func_get_args() as $arg)
	        if(empty($arg))
	            continue;
	        else
	            return false;
	    return true;
	}

	static function should_init() {
		return self::get_setting('database') && Query::connected() !== false || self::get_setting('static_posts_dir');
	}

	static function bpf($string, array $placeholders = array()) {

		if( !empty($string) ) {
			$blogname = self::get_setting('blogname', 'A BlogPad Blog');
			$blogdescription = self::get_setting('blogdescription', 'An (awesome) blog.');

			$posttitle = isset($placeholders['posttitle']) ? $placeholders['posttitle']: '';
			$category = isset($placeholders['category']) ? $placeholders['category']: '';
			$searchquery = isset($placeholders['searchquery']) ? $placeholders['searchquery']: '';

			$username = isset($placeholders['username']) ? $placeholders['username']: '';

			// reps = replacements
			$reps = array(
				'blogname' => $blogname,
				'blogdescription' => $blogdescription,
				'posttitle' => $posttitle,
				'category' => $category,
				'searchquery' => $searchquery,
				'username' => $username
			);

			foreach( $reps as $type => $val ) {
				$string = str_replace("%$type%", $val, $string);
			}

			return $string;
		}

		return null;
	}

	
}