<?php

class BP_Parser {

	protected static $parts = array(
		'php_tags',
		'gets',
		'vars',
		'var_calls',
		'conditionals',
		'posts',
		'metas',
		'pagination',
		'when'
	);

	static function parse_bpd($filepath = null, $raw = false) {
		if( is_null($filepath) ) {
			trigger_error('Please provide a file to parse.', E_USER_ERROR);
			exit;
		}

		if( !is_file($filepath) ) {
			trigger_error("Couldn't find $filepath.", E_USER_ERROR);
			exit;
		}

		$contents = file_get_contents($filepath);

		if( preg_match('/{\- FILE STRUCT \-}/', $contents) ) {
			return BP_Parser::parse_file_struct($contents, $filepath);
		} 

		else if( preg_match('/{\- URL STRUCT \-}/', $contents) ) {
			return BP_Parser::parse_url_struct($contents, $filepath, $raw);
		}

		else {
			trigger_error("Couldn't find any defined structs in $filepath.", E_USER_ERROR);
			exit;
		}

	}

	protected static function parse_file_struct($contents, $filepath) {

		if( preg_match('/{\- FILE STRUCT \-}/', $contents) ) {

			$struct = array();

			preg_match_all('/{\- BEGIN FILES \-}(.*?){\- END FILES \-}/s', $contents, $_struct);

			foreach( $_struct[1] as $declaration ) {

				preg_match_all('/{\- (HOMEPAGE|STYLESHEET|POST|PROFILE|CATEGORY|URL_STRUCT|ERROR|SEARCH)\: (.*?) \-}/', $declaration, $file_struct);

				if( !empty($file_struct) ) {				

					$paths = array();

					$required = array(
						'HOMEPAGE',
						'STYLESHEET',
						'POST',
						'URL_STRUCT',
						'CATEGORY'
					);

					foreach( $required as $type ) {

						if( !in_array($type, $file_struct[1]) ) {
							trigger_error("`$type` is a required struct which you haven't defined in $filepath.", E_USER_ERROR);
							exit;
						}
					}

					foreach( $file_struct[1] as $index => $type ) {

						$value = $file_struct[2][$index];

						// Other than a stylesheet or a link structure definition file, every other template must end in a .bp extension.
						if( $type !== 'STYLESHEET' && $type !== 'URL_STRUCT' ) {
							$paths[$type] = $value.'.bp';
						} 

						else {

							if( $type === 'STYLESHEET' ) {
								$paths[$type] = $value.'.css';
							} 

							else {
								$paths[$type] = $value.'.bpd';
							}
						}
					}

					$struct['paths'] = $paths;
				}

			}
		}

		return $struct;

	}

	protected static function parse_url_struct($contents, $filepath, $raw = false) {

		if( preg_match('/{\- URL STRUCT \-}/', $contents) ) {

			$urls = array();

			preg_match('/{\- BEGIN URLS \-}(.*?){\- END URLS \-}/s', $contents, $declaration);

			if( empty($declaration) ) {
				trigger_error("Couldn't find any URL structures defined in $filepath.", E_USER_ERROR);
				exit;
			}

			preg_match_all('/{\- PATTERN\: (.*?), TEMPLATE\: ([A-z]+) \-}/', $declaration[1], $definitions);

			if( !empty($definitions) ) {

				$replacements = array(
					'word' => '[A-z%0-9\_\s]+',
					'num' => '[0-9]+',
					'slug' => '[\w\-]+'
				);

				$params = array(
					'CATEGORY' => 'word=$1&pagenum=$2',
					'POST' => '_post=$1',
					'HOMEPAGE' => 'pagenum=$1',
					'PROFILE' => 'username=$1&pagenum=$2',
					'SEARCH' => 'query=$1&pagenum=$2'
				);

				foreach( $definitions[1] as $index => $url ) {

					$template = $definitions[2][$index];

					// when raw, the conversion will not take place.
					if( !$raw ) {

						if( preg_match_all('/%([a-z]+)%/', $url, $shortcodes) ) {

							foreach( $shortcodes[1] as $shortcode ) {

								if( array_key_exists($shortcode, $replacements) ) {
									$url = str_replace("%$shortcode%", "({$replacements[$shortcode]})", $url);
								}
							}
						}
					}

					$urls[$url] = array(
						'template' => $template,
						'params' => $params[$template]
					);

				}
				
			}

			return $urls;
		}

		return false;
	}


	/**
	 * Processes the content of a static BlogPad post.
	 * @return array
	 * 
	 */
	  
	static function parse_bpp($filepath = null) {
		if( is_null($filepath) ) {
			trigger_error("Please provide a filepath to parse.", E_USER_ERROR);
			exit;
		}

		if( !preg_match('/\.bpp$/', $filepath) ) {
			trigger_error('parse_bpp method can only parse .bpp files.', E_USER_ERROR);
			exit;
		}

		$contents = file_get_contents($filepath);

		if( preg_match('/\-\- BEGIN METADATA(.*?)\-\- END METADATA/s', $contents, $holders)) {

			preg_match_all('/(Title|Date|Author|Categories|Description)\: (.*)/', $holders[1], $meta);
						
			if( !empty($meta) ) {

				$required = array(
					'Title',
					'Date',
					'Author',
					'Categories',
					'Description'
				);

				foreach( $required as $type ) {

					if( !in_array($type, $meta[1]) ) {
						trigger_error("`$type` must be provided in $filepath.", E_USER_ERROR);
						exit;
					}
				}
								
				foreach( $meta[1] as $index => $type ) {

					$data = $meta[2][$index];

					if( trim($data) === '' ) {
						trigger_error("`$type` must not be left empty in $filepath.", E_USER_ERROR);
						exit;
					}

					if( $type === 'Date' ) {
						$data = strtotime($data);
					}

					$posts[ strtolower($type) ] = $data;
				}

			}	

			preg_match('/\-\- END METADATA(.*)/s', $contents, $post);

			if( empty($post[1]) ) {
				trigger_error("Couldn't find a post in $filepath.", E_USER_ERROR);
				exit;
			}

			$posts['post'] = trim( preg_replace('/^\n/', '', $post[1]));
		}

		else {
			trigger_error("BlogPad couldn't find a post in $filepath.", E_USER_ERROR);
			exit;
		}

		return $posts;

	}

	/**
	 * Generates a PHP template for the .bp template provided.
	 * @return string|error
	 * 
	 */

	protected static function gen($file) {

		$compile = BP_Parser::compile($file);

		$root = BlogPad::get_templates_dir();

		$theme = BlogPad::get_theme_name();

		$template_name = 'compiled_'.basename($file).'.php';

		if( !is_dir($root) ) {
			mkdir($root);
		}

		// Organise each template by its theme directory.
		if( !is_dir("$root/$theme") ) {
			mkdir("$root/$theme");
		}

		if( !empty($compile) ) {
			if( !file_put_contents("$root/$theme/$template_name", $compile) ) {
				trigger_error("Couldn't generate template for $file.", E_USER_NOTICE);
				exit;
			}

		}

		// Return the path of the compiled template so that it can be used as a pointer.
		return "$root/$theme/$template_name";
	}
	
	/**
	 * Compiles the provided string treating it as BlogPad code.
	 * @return string|null
	 * 
	 */

	protected static function compile($file) {

		$content = file_get_contents($file);
		
		foreach( BP_Parser::$parts as $part ) {
			$content = call_user_func("BP_Parser::handle_$part", $content, dirname($file) );
		}

		return $content;

	}

	/**
	 * Removes PHP tags in templates.
	 * @return string|null
	 * 
	 */ 

	protected static function handle_php_tags($content) {
		return preg_replace('/\<\?(php|=)(.*?)\?\>/s', '', $content);
	}

	/**
	 * Compiles BlogPad get tags and processes their content.
	 * @return string|null
	 * 
	 */

	protected static function handle_gets($content) {

		$root = func_get_arg(1);
		
		preg_match_all('/{\- GET [\'"](.*?)[\'"] \-}/', $content, $gets);

		if( !empty($gets[0]) ) {

			$_parts = $gets[1];

			foreach( $_parts as $index => $__part ) {

				if( !is_file("$root/$__part.bp") ) {
					trigger_error("Couldn't load part '$__part' (.bp) as it wasn't found in $root.", E_USER_ERROR);
					exit;
				}

				// Since there's no telling what can be in an included template, let's just run the whole process over again.
				foreach( BP_Parser::$parts as $part ) {
					$content = str_replace($gets[0][$index], call_user_func("BP_Parser::handle_$part", file_get_contents("$root/$__part.bp"), dirname("$root/$__part.bp") ), $content);
				}
			}

		}

		return $content;
	}

	/**
	 * Converts BlogPad date tags into the PHP equivalent.
	 * @return string|null
	 * 
	 */

	protected static function handle_dates($content) {

		preg_match_all('/{\- date(-updated)?\:([A-z,\/\-\'"\s%]+) \-}/', $content, $dates);

		if( !empty($dates[0]) ) {

			$placeholders = array(
				'day' => 'l',
				'date' => 'jS',
				'month' => 'F',
				'year' => 'Y'
			);

			$formats = $dates[2];

			foreach( $formats as $index => $format ) {

				$format = trim($format);

				$update = false;

				if( preg_match_all('/%([a-z]+)%/', $format, $_placeholders) ) {

					if( trim($dates[1][$index]) === '-updated') {
						$update = true;
					} 

					foreach( $_placeholders[1] as $label => $_format ) {

						if( array_key_exists($_format, $placeholders) ) {
							$format = str_replace("%$_format%", $placeholders[$_format], $format);
						} 
					}
				}

				if( $update ) {
					$content = str_replace($dates[0][$index], "<?php echo date('$format', \$post['updated']); ?>", $content);
				}

				else {
					$content = str_replace($dates[0][$index], "<?php echo date('$format', \$post['date']); ?>", $content);
				}
				
			}

			
		}

		return $content;
	}

	/**
	 * Processes calls to retrieve categories. Also grabs anything surrounding the call.
	 * @return string
	 * 
	 */

	protected static function handle_categories($content) {

		$before = '<?php $catlist = BlogPad::ser_to_list($post["categories"]); foreach( (strpos($catlist, ",") ? explode(",", $catlist): array($catlist) ) as $category): ?>';

		$after = '<?php endforeach; ?>';

		if( BlogPad::has_setting('auto_link', true) ) {
			return preg_replace('/(.*?){\- categories \-}(.*)/', $before.'$1<a href="<?php echo Link_Parser::generate_link("category", array("word" => $category));?>"><?php echo $category;?></a>$2'.$after, $content);
		}

		preg_match('/.*?{\- categories \-}.*/', $content, $cats);

		$cat = isset($cats[0]) ? $cats[0]: '';

		if( !empty($cat) ) {

			if( preg_match('/{\- category \-}/', $cat) ) {
				$cat = preg_replace('/{\- categor(ies|y) \-}/', '<?php echo $category;?>', $cat);
			}

			$content = str_replace($cats[0], $before.$cat.$after, $content);
		}

		return $content;

	}

	protected static function handle_var_calls($content) {

		$content =  preg_replace('/{\- VAR ([A-z]+) \-}/', '<?php echo \$$1; ?>', $content);

		$content = preg_replace('/{\-\- VAR ([A-z]+) \-\-}/', '\$$1', $content);

		return $content;
	}

	protected static function handle_conditionals($content) {

		preg_match_all('/{\- (IF|ELSE IF) (.*)\B-|ELSE\B-}/', $content, $ifs);

		if( !empty($ifs[0]) ) {

			foreach( $ifs[0] as $index => $condition ) {

				$type = strtolower($ifs[1][$index]);

				$cond = (isset($ifs[2][$index]) && !empty($ifs[2][$index]) ) ? $ifs[2][$index]: '';

				if( !empty($cond) ) {
					$content = str_replace($condition.'}', "<?php $type($cond): ?>", $content);
				}

				$content = str_replace('{- ELSE -}', '<?php else: ?>', $content);
				
				$content = str_replace('{- END IF -}', '<?php endif; ?>', $content);
			}
		}

		return $content;
	}

	/**
	 * Compiles BlogPad variables into valid PHP variables.
	 * @return string|null
	 * 
	 */

	protected static function handle_vars($content) {
		return preg_replace('/{\- VAR ([a-z_]+)\: (.*?) \-}/', '<?php \$$1 = $2; ?>', $content);
	}

	/**
	 * Renders post blocks into valid PHP blocks.
	 * @return string
	 * 
	 */ 

	protected static function handle_posts($content) {
		
		preg_match('/{\- BEGIN POST(S)? \-}.*?{\- END POST(S)? \-}/s', $content, $post_loop_block);

		// $posts or $post? 
		$is_s = isset($post_loop_block[1]) && strtoupper( trim($post_loop_block[2]) ) === 'S';

		if( !empty($post_loop_block[0]) ) {
			
			// excerpt, slug, and description are those indexes which don't require additional processing.
			$_content = preg_replace('/{\- (excerpt|slug|description) \-}/', '<?php echo $post[\'$1\']; ?>', $post_loop_block[0]);

			if( BlogPad::has_setting('auto_link', true) ) {

				$_content = str_replace('{- title -}', "<a href=\"<?php echo Link_Parser::generate_link('post', array('slug' => \$post['slug']));?>\"><?php echo \$post['title'];?></a>", $_content);

				$_content = str_replace('{- author -}', "<a href=\"<?php echo Link_Parser::generate_link('profile', array('slug' => \$post['author']));?>\"><?php echo \$post['author'];?></a>", $_content);
			}

			else {
				$_content = str_replace('{- title -}', "<?php echo \$post['title'];?>", $_content);

				$_content = str_replace('{- author -}', "<?php echo \$post['author'];?>", $_content);
			}

			$_content = str_replace('{- post -}', "<?php echo Parsedown::instance()->parse(\$post['post']);?>", $_content);

			$_content = str_replace('{- BEGIN POST'.( ($is_s) ? 'S': '').' -}', '<?php if( !empty($post'.( ($is_s) ? 's': '').') ): '.( ($is_s) ? 'foreach($posts as $post):' : '').' ?>', $_content);

			$_content = str_replace('{- END POST'.( ($is_s) ? 'S': '').' -}', '<?php '.( ($is_s) ? 'endforeach; else: ': 'else: ')."echo BlogPad::get_setting('no_post_message', 'Nothing to see here!'); endif; ?>", $_content);


			// Now parse date and categories calls
			$_content = BP_Parser::handle_dates($_content);
			$_content = BP_Parser::handle_categories($_content);

			$content = str_replace($post_loop_block[0], $_content, $content);
		}

		return $content;
	}

	/**
	 * Converts metadata tags into the PHP access equivalent.
	 * @return string|null
	 * 
	 */

	protected static function handle_metas($content) {
		return preg_replace('/{\- metadata: (title|description|id) \-}/', '<?php echo $metadata[\'$1\']; ?>', $content);
	}

	protected static function handle_pagination($content) {

		/**
		 * Forgive me for the (extremely) messy code in this function. Its purpose is to provide a pagination layout on a * page. That's it, as well as insert URL's if specified.
		 * 
		 * 
		 */ 

		preg_match('/{- BEGIN PAGINATION -}.*?{- END PAGINATION -}/s', $content, $pnation);

		$autolink = BlogPad::has_setting('auto_link');

		$p_processed = '';

		if( !empty($pnation[0]) ) {

			$txt = $pnation[0];

			if(preg_match('/{- BEGIN COUNTDOWN -}.*?{- END COUNTDOWN -}/s', $pnation[0], $cdown) ) {
				$_cdown = str_replace('{- BEGIN COUNTDOWN -}', '<div class="bp-countdown"><?php $i = 1; while($i <= $paginate["last_page"]): ?>', $txt);

				$_cdown = preg_replace('/(.*?){- number -}(.*)/', ($autolink ? '$1<a href="<?php echo Link_Parser::generate_link(BlogPad::$current_file, array("num" => $i, "word" => (isset($word)) ? $word: ""));?>"><?php echo $i;?></a>$2': '$1<?php echo $i;?>$2'), $_cdown);

				$_cdown = str_replace('{- END COUNTDOWN -}', '<?php $i++; endwhile; ?></div>', $_cdown);

				$txt = str_replace($pnation[0], $_cdown, $txt);
			}

			// Support the placement of custom text that will be used as a label, else display the relevant page numbers.
			$rplace = preg_replace('/(.*?){- previous:?([A-z0-9\s]+)? -}(.*)/', '<?php if( !($pagenum - 1 <= 0) ): ?>$1'.($autolink ? '<a href="<?php echo Link_Parser::generate_link(BlogPad::$current_file, array("num" => $pagenum - 1, "word" => (isset($word)) ? $word: ""));?>"><?php echo (trim("$2") !== "") ? "$2": $pagenum - 1;?></a>': '<?php echo (trim("$2" !== "")) ? "$2": $pagenum - 1;?>').'$3<?php endif;?>', $txt);


			$rplace = preg_replace('/(.*?){- next:?([A-z0-9\s]+)? -}(.*)/', '<?php if($pagenum + 1 > 1 && $pagenum < $paginate["last_page"]): ?>$1'.($autolink ? '<a href="<?php echo Link_Parser::generate_link(BlogPad::$current_file, array("num" => $pagenum + 1, "word" => (isset($word)) ? $word: ""));?>"><?php echo (trim("$2") !== "") ? "$2": $pagenum + 1;?></a>': '<?php echo (trim("$2") !== "") ? "$2": $pagenum + 1;?>').'$3<?php endif;?>', $rplace);

			$p_processed .= $rplace;

			$p_processed = str_replace('{- BEGIN PAGINATION -}', '<div class="bp-pagination">', $p_processed);
			$p_processed = str_replace('{- END PAGINATION -}', '</div>', $p_processed);

			$content = str_replace($pnation[0], $p_processed, $content);
		}

		return $content;
	}

	protected static function handle_when($contents) {
		return preg_replace('/{- WHEN ([\'"].*?[\'"]) -}(.*?){- END WHEN -}/s', '<?php if(BlogPad::$current_file === $1):?>$2<?php endif;?>', $contents);
	}
}

?>
