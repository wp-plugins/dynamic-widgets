<?php
/**
 * Author Module
 *
 * @version $Id$
 * @copyright 2011 Jacco Drabbe
 */

	class DW_Author extends DWModule {
		protected static $except = 'Except the author(s)';
		public static $option = array( 'author' => 'Author Pages' );
		protected static $question = 'Show widget default on author pages?';
		protected static $type = 'complex';

		public static function admin() {
			parent::admin();

			$list = self::getAuthors();
			self::mkGUI(self::$type, self::$option[self::$name], self::$question, FALSE, self::$except, $list);
		}

		public static function getAuthors() {
			global $wpdb;

			if ( function_exists('get_users') ) {
				$authors = get_users( array('who' => 'authors') );
			} else {
				$query = "SELECT " . $wpdb->prefix . "users.ID, " . $wpdb->prefix . "users.display_name
							 FROM " . $wpdb->prefix . "users
							 JOIN " . $wpdb->prefix . "usermeta ON " . $wpdb->prefix . "users.ID = " . $wpdb->prefix . "usermeta.user_id
							 WHERE 1 AND " . $wpdb->prefix . "usermeta.meta_key = '" . $wpdb->prefix . "user_level'
							 	AND " . $wpdb->prefix . "usermeta.meta_value > '0'";
				$authors = $wpdb->get_results($query);
			}

			$list = array();
			foreach ( $authors as $author ) {
				$list[$author->ID] = $author->display_name;
			}

			return $list;
		}
	}
?>

