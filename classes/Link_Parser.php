<?php

class Link_Parser extends BlogPad {

	/**
	 * Initializes the class and runs checks on URL's against defined structures.
	 * @return null
	 * 
	 */ 

	static function load() {

		foreach( BlogPad::get_url_struct() as $regex => $actions ) {

			$regex = str_replace('/', '\/', $regex);

			if( preg_match("/$regex/", Link_Parser::current_uri() ) ) {

				$params = preg_replace("/$regex/", $actions['params'], BlogPad::current_uri() );

				if( !strpos($params, '&') ) {
					$explode = explode('=', $params);

					BlogPad::$vars[ $explode[0] ] = $explode[1];
				}

				else {

					foreach( explode('&', $params) as $query ) {
						
						$explode = explode('=', $query);

						BlogPad::$vars[ $explode[0] ] = $explode[1];
					}
				}

				BlogPad::$to_load = $actions['template'];

			}

		}
	}

	static function generate_link($struct = null, array $options = array() ) {
		if( is_null($struct) ) {
			trigger_error('Please specify a struct.', E_USER_ERROR);
			exit;
		}
		
		$url = BlogPad::get_blog_homepage();

		$convs = array(
			'%word%' => isset($options['word']) ? trim($options['word']): '',
			'%slug%' => isset($options['slug']) ? trim($options['slug']): '',
			'%num%' => isset($options['num'])? (int) $options['num']: 0
		);

		foreach( BlogPad::get_url_struct(true) as $_url => $meta ) {

			if( strtoupper($meta['template']) === strtoupper($struct) ) {
				
				foreach( $convs as $pholder => $value ) {

					if(strpos($_url, $pholder) && empty($value)) {
						$_url = str_replace("{$pholder}?", '', $_url);
						$_url = preg_replace('/[\W\w]\?/', '', $_url);
					}

					if( !empty($value) ) {
						$_url = str_replace($pholder, $value, $_url);
					}

				}

				$_url = preg_replace('/(?<=[\W\w])\?/', '', $_url);

				$url = $url.'/'.$_url;
			}
		}

		return $url;
	}

	/**
	 * Returns the value of $_SERVER['REQUEST_URI'] removing the base directory from the URL.
	 * @return string
	 * 
	 */ 

	static function current_uri() {
    		return preg_replace('/^\/?'.basename(BlogPad::get_setting('base')).'\/?/', '', $_SERVER['REQUEST_URI']); 
	}

	/**
	 * Returns the URL of the current webpage in its current form, additionally allowing the ability to change
	 * values of keys in the query string, if present.
	 * 
	 * @return str
	 * 
	 */ 

	static function current_url($key = null, $value = null) {

		$url  = ( isset($_SERVER['HTTPS']) && $_SERVER["HTTPS"] === 'on' ) ? 'https://'.$_SERVER["SERVER_NAME"] :  'http://'.$_SERVER["SERVER_NAME"];
		$url .= $_SERVER["REQUEST_URI"];

		if( !is_null($key) && !is_null($value) ) {

			if( preg_match("/$key=[\w\-%_.+!*'\"()]+/", $url, $x) ) {
				$url = preg_replace("/$key=[\w\-%_.+!*'\"()]+/", "$key=$value", $url);
			}

			else {
				if( !preg_match('/\?[\w\W]+=/', $url) ) {
					$url .= "?$key=$value";
				}

				else {
					$url .= "&$key=$value";
				}
			}
		}

		else if( is_array($key) ) {
			foreach( $key as $_key => $_value ) {

				if( preg_match("/$_key=[\w\W]+/", $url) ) {
					$url = preg_replace("/$_key=[\w\W]+/", "$_key=$_value", $url);
				}

				else {
					if( !preg_match('/\?[\w\W]+=/', $url) ) {
						$url .= "?$_key=$_value";
					}

					else {
						$url .= "&$_key=$_value";
					}
				}
			}
		}

		return $url;
	}
}

?>
