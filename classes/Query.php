<?php

	class Query {

		// Does Query have all the required information it needs to continue? Set to false now, but change to true later on if applicable.
		protected static $setup_complete = false;

		// Store all custom queries in this array.
		protected static $custom_queries = array();

		// Short for 'instance'. Store the PDO instance in this variable.
		protected static $inst = null;

		// Store the number of rows returned from the previous query here.
		protected static $numrows = 0;

		// Store the name of the database in here during setup.
		private static $dbname = '';

		/**
		 * Return the connection instance.
		 * @return Object
		 *
		 */

		static function connection() {
			return self::$inst;
		}

		static function close_connection() {
			return self::$inst = null;
		}


		/**
		 * A soggy attempt at detecting whether the code provided could be SQL code.
		 *
		 * @param string $code
		 * @return bool
		 */

		private function could_be_sql( $code = '' ) {

			$regex = '/(SELECT|UPDATE|DELETE|DESCRIBE|EXPLAIN|CREATE|ALTER|DROP|INSERT|SHOW) (FROM|INTO|.*) (FROM|SET|.*) (.*)/';

			return ( preg_match( $regex, $code ) ) ? true : false;
		}

		/**
		 * Check whether the current version of PHP can support custom formatted queries. PHP needs to be on at least 5.3.0 as support for closures was added on then.
		 * @return boolean
		 */

		private static function can_custom_build() {
			return version_compare( PHP_VERSION, '5.3.0', '>=' );
		}

		static function connected() {
			if( is_null( self::connection() ) ) {
				return false;
			}

			return self::connection()->getAttribute(PDO::ATTR_CONNECTION_STATUS);
		}

		/**
		 * Check whether Query.php can run on the current version of PHP.
		 *
		 * @return boolean
		 */

		private static function can_query_run() {
			return version_compare( PHP_VERSION, '5.1.0', '>=' );
		}

		/**
		 * Function that constructs a custom query format.
		 * @uses self::$custom_queries
		 * @param $query
		 * @param callable $closure
		 * @return null
		 */

		static function build( $query, Closure $closure ) {

			if ( !self::$setup_complete ) {
				return false;
			}

			// Abandon ship if custom queries can't be built.

			if ( !self::can_custom_build() ) {

				die( sprintf( '<strong>Error</strong>: Oops, it looks like your version of PHP (%s) doesn\'t support custom formatted queries. Visit the <a href="%s">Github Docs</a> for more information on this error.', PHP_VERSION, 'https://github.com/sharikul/Query.php/blob/master/help/support.md#oops-it-looks-like-your-version-of-php-doesnt-support-custom-formatted-queries' ) );

			}

			$query = trim( preg_replace( "/%c/", "(.*)", $query ) );

			if ( !array_key_exists( $query, self::$custom_queries ) ) {
				self::$custom_queries[$query] = $closure;
			}
		}

		/**
		 * Shorthand select method.
		 *
		 * @uses self::_query
		 *
		 * @param string $column
		 * @param string $value
		 * @param string $table
		 * @param string $specific_column
		 * @param array $placeholders
		 * @return Object
		 */

		static function select_where( $column = '', $value = '', $table = '', $specific_column = '', array $placeholders = null ) {

			if ( !isset( $placeholders ) ) {
				$value = '"' . $value . '"';
			}


			return self::_query( $specific_column, $table, array(
				'where' => $column . ' = ' . $value,
				'placeholders' => $placeholders
			) );
		}

		/**
		 * Shorthand update method. Accept the necessary arrays (placeholders and update) via a wrapper array($options)
		 *
		 * @uses self::_query
		 *
		 * @param string $column
		 * @param string $value
		 * @param string $table
		 * @param array $options
		 * @return Object
		 */

		static function update_where( $column = '', $value = '', $table = '', array $options = null ) {


			$placeholders = ( array_key_exists( 'placeholders', $options ) && gettype( $options['placeholders'] ) === 'array' ) ? $options['placeholders'] : array();
			$update       = ( array_key_exists( 'update', $options ) && gettype( $options['update'] ) === 'array' ) ? $options['update'] : null;

			return self::_query( '', $table, array(
				'action' => 'update',
				'where' => $column . ' = :value',
				'update' => $update,
				'placeholders' => array_merge( array(
					':value' => $value
				), $placeholders )
			) );
		}


		/**
		 * Check whether the specified column exists in the specified table.
		 *
		 * @param string $column
		 * @param string $table
		 *
		 * @return bool
		 */

		static function column_exists( $column = '', $table = '' ) {
			if ( !empty( $column ) && !empty( $table ) ) {

				$query = self::run( "SHOW COLUMNS FROM $table LIKE '$column'" );

				return ( !empty( $query ) ) ? true : false;
			}

			return false;
		}


		/**
		 * Check whether the specified table exists in the database.
		 *
		 * @param string $table
		 *
		 * @return bool
		 */

		static function table_exists( $table = '' ) {
			if ( !empty( $table ) && !empty( self::$dbname ) ) {

				$query = Query::run( "SHOW TABLES LIKE '$table'" );

				return ( !empty( $query ) );
			}

			return false;
		}

		/**
		 * Function that executes queries, both SQL and custom formats.
		 *
		 * @param $query
		 * @param array $placeholders
		 * @return Object
		 */

		static function run( $query, array $placeholders = null ) {

			if ( !self::$setup_complete ) {
				return false;
			}

			// Query only supports a set of MySQL actions. Limit them here.
			$valid_query_sql = array(
				"INSERT",
				"UPDATE",
				"DELETE",
				"SELECT",
				"DESCRIBE",
				"EXPLAIN",
				"SHOW"
			);

			$explode = explode( " ", $query );

			if ( in_array( $explode[0], $valid_query_sql ) ) {

				if ( self::could_be_sql( $query ) ) {

					$quer = self::normal_sql( $query, $placeholders );

					self::$numrows = count( $quer );

					return $quer;
				}
			} else {
				return self::exec_build( $query );
			}
		}

		/**
		 * The big-boy function that handles generating SQL queries based on the action provided.
		 *
		 * @uses self::normal_sql
		 *
		 * @param string $column
		 * @param string $table
		 * @param array $extra_options
		 * @return Object
		 */

		static function _query( $column = '', $table = '', array $extra_options = null ) {

			if ( !self::$setup_complete || !self::connected() ) {
				return false;
			}

			$query = '';

			$column = ( empty( $column ) ) ? '*' : $column;

			if ( isset( $column ) && isset( $table ) ) {

				$action = ( isset( $extra_options ) && array_key_exists( 'action', $extra_options ) ) ? strtoupper( trim( $extra_options['action'] ) ) : 'SELECT';


				$limit_start = ( isset( $extra_options ) && array_key_exists( 'limit_start', $extra_options ) ) ? trim( $extra_options['limit_start'] ) : 0;

				$limit_end = ( isset( $extra_options ) && array_key_exists( 'limit_end', $extra_options ) ) ? trim( $extra_options['limit_end'] ) : 0;

				$order_by = ( isset( $extra_options ) && array_key_exists( 'order_by', $extra_options ) ) ? trim( $extra_options['order_by'] ) : '';

				$sort = ( isset( $extra_options ) && array_key_exists( 'sort', $extra_options ) ) ? strtoupper( trim( $extra_options['sort'] ) ) : '';

				$where = ( isset( $extra_options ) && array_key_exists( 'where', $extra_options ) ) ? $extra_options['where'] : '';

				$placeholders = ( isset( $extra_options ) && array_key_exists( 'placeholders', $extra_options ) && gettype( $extra_options['placeholders'] ) === 'array' ) ? $extra_options['placeholders'] : null;

				$update_sets = ( isset( $extra_options ) && array_key_exists( 'update', $extra_options ) && gettype( $extra_options['update'] ) === 'array' ) ? $extra_options['update'] : '';




				if ( $action === 'SELECT' ) {
					$query .= 'SELECT ' . $column . ' FROM ' . $table;

					if ( !empty( $where ) ) {
						$query .= ' WHERE ' . $where;
					}

					if ( !empty( $order_by ) ) {
						$query .= ' ORDER BY ' . $order_by;
					}

					if ( !empty( $sort ) && !empty($order_by)) {
						$query .= ' ' . $sort;
					}

					if ( !is_null( $limit_start ) && !is_null( $limit_end ) && !$limit_end <= 0 ) {
						$query .= ' LIMIT ' . $limit_start . ', ' . $limit_end;
					}

				}

				else if ( $action === 'INSERT' ) {

					$values = ( array_key_exists( 'values', $extra_options ) ) ? $extra_options['values'] : '';

					if ( !empty( $values ) ) {
						if ( gettype( $values ) === 'array' ) {

							$val     = '';
							$val_len = count( $values ) - 1;
							$i       = 0;

							foreach ( $values as $value ) {
								if ( $i++ !== $val_len ) {
									if( $value[0] !== ":") {
										$val .= "'$value'" . ', ';
									} else {
										$val .= $value.', ';
									}
								} else {
									if( $value[0] !== ":") {
										$val .= "'$value'";
									} else {
										$val .= $value;
									}
								}
							}
						}
					}

					if ( isset( $val ) ) {
						$values = $val;
					}

					$query .= 'INSERT INTO ' . $table . ' (' . $column . ') VALUES (' . $values . ')';
				}

				else if ( $action === 'UPDATE' ) {

					$query .= 'UPDATE ' . $table;

					// Append this string to $query after the foreach loop
					$col_set = '';

					if ( !empty( $update_sets ) ) {

						$i       = 0;
						$set_len = count( $update_sets ) - 1;

						foreach ( $update_sets as $column => $value ) {
							$value = str_replace('"', '\"', $value);
							$value = ( $value[0] !== ':' ) ? '"' . $value . '"' : $value;
							if ( $i++ !== $set_len ) {
								$col_set .= $column . ' = ' . $value.', ';
							} else {
								$col_set .= $column . ' = ' . $value;
							}
						}


						$query .= ' SET ' . $col_set;

					}

					if ( !empty( $where ) ) {
						$query .= ' WHERE ' . $where;
					}

				} else if ( $action === 'DESCRIBE' ) {
					$query .= 'DESCRIBE ' . $table;
				}

				else if ( $action === 'EXPLAIN' ) {
					if ( array_key_exists( 'custom', $extra_options ) ) {
						$query .= 'EXPLAIN ' . $extra_options['custom'];
					}
				}

				else if ( $action === 'CUSTOM' ) {
					if ( array_key_exists( 'custom', $extra_options ) ) {
						$query = $extra_options['custom'];
					}
				}

				else if ( $action === 'DELETE' ) {

					if ( !empty( $where ) && !empty( $table ) ) {
						$query .= 'DELETE FROM ' . $table . ' WHERE ' . $where;
					}
				}

				// Time for execution!
				return self::run( $query, $placeholders );
			}
		}

		/**
		 * Function that gets Query connected to the database.
		 * @uses self::can_run()
		 *
		 * @note: Execute self::can_run() before continuing. If script execution ends here, then no other method would be able to execute.
		 * @param array $options
		 */

		static function setup( array $options ) {

			// Don't continue execution if setup has already been completed
			if ( self::$setup_complete ) {
				return;
			}

			if ( !self::can_query_run() ) {
				echo sprintf( '<h1 style="font-family: sans-serif; font-weight: lighter;">Query.php isn\'t compatible with your version of PHP (%s). Please upgrade to at least 5.1.0 to continue.</h1>', PHP_VERSION );
				exit();
			}

			// 127.0.0.1 instead of "localhost" as if would prevent a DNS lookup thus improving connection speed, according to Alex Garrett on http://www.youtube.com/watch?v=QRPAxAtxdmQ.
			$host     = ( array_key_exists( 'host', $options ) ) ? $options['host'] : '127.0.0.1';
			$username = ( array_key_exists( 'username', $options ) ) ? $options['username'] : 'root';
			$password = ( array_key_exists( 'password', $options ) ) ? $options['password'] : '';
			$database = ( array_key_exists( 'database', $options ) ) ? $options['database'] : '';
			$driver   = ( array_key_exists( 'driver', $options ) ) ? $options['driver'] : 'mysql';


			if ( !empty( $database ) ) {

				self::$dbname = $database;

				// Invoke PDO! Store the instance in the $inst property

				if ( class_exists( 'PDO' ) ) {

					if ( $driver === 'mysql' ) {

						try {
							self::$inst = new PDO( 'mysql:host=' . $host . ';dbname=' . $database, $username, $password );

							self::$setup_complete = true;
						}

						catch ( Exception $error ) {
							return;
						}
					}

					else if ( $driver === 'sqlite' ) {

						if ( array_key_exists( 'sqlite_path', $options ) ) {

							try {
								self::$inst = new PDO( 'sqlite' . $options['sqlite_path'] );

								self::$setup_complete = true;
							}

							catch ( Exception $error ) {
								return;
							}
						}
					}

					else {
						die( sprintf( '<strong>Error</strong>: Driver "<code>%s</code>" is not supported by Query.php. Check out the <a href="%s">Github Docs</a> for more information on this error.', $driver, 'https://github.com/sharikul/Query.php/blob/master/help/support.md#driver-is-not-supported-by-queryphp' ) );
					}

				}

				else {
					die( sprintf( '<strong>Error</strong>: Query.php requires the PDO module to be installed. Check out the <a href="%s">Github Docs</a> for more information on this error.', 'https://github.com/sharikul/Query.php/blob/master/help/support.md#queryphp-requires-the-pdo-module-to-be-installed' ) );
				}

			}
		}


		/**
		 * Return the value of the specified index from the provided $results array
		 *
		 * @usage: Query::get_var( array('name' => 'Query.php', 'dob' => 'September 2013'), 'name')
		 *
		 * @param array $results
		 * @param       $index
		 *
		 * @return mixed
		 */

		static function get_var( array $results, $index ) {

			if ( array_key_exists( $index, $results ) ) {
				return $results[$index];

			} else if ( array_key_exists( $index, $results[0] ) ) {
				return $results[0][$index];
			}

			return false;
		}

		/**
		 * Function that executes regular SQL code, with support for PDO placeholders.
		 *
		 * @param $sql
		 * @param array $placeholders
		 * @return Array
		 */

		static function normal_sql( $sql, array $placeholders = null ) {

			$db = self::connection();

			// Execution time!
			$query = $db->prepare( $sql );

			$query->execute( $placeholders );

			return $query->fetchAll();
		}

		/**
		 * Function that processes and executes custom query builds.
		 *
		 * @uses self::$custom_queries
		 *
		 * @param $query
		 * @return mixed
		 */

		static function exec_build( $query ) {

			foreach ( self::$custom_queries as $key => $value ) {

				if ( substr( $key, 0, 4 ) === substr( $query, 0, 4 ) ) {

					preg_match_all( "/$key/", $query, $matches );

					$args = array();

					// Remove the first element as it's completely useless for our needs.
					unset( $matches[0] );

					for ( $i = 1; $i <= count( $matches ); $i++ ) {
						$findings = ( array_key_exists( 0, $matches[$i] ) ) ? $matches[$i][0] : '';

						if ( !empty( $findings ) ) {
							array_push( $args, $findings );
						} else {

							// Nothing returned. Could the expression be invalid?
							printf( 'Unrecognised expression: <strong>%s</strong>. Visit <a href="%s">the GitHub support page</a> for assistance.', $query, 'https://github.com/sharikul/Query.php/blob/master/help/support.md#unrecognised-expression' );
							exit();
						}
					}

					// This will execute the closure provided, and set the parameters provided to the closure equal to the corresponding array value.
					return call_user_func_array( $value, $args );

				}
			}
		}
	}
?>