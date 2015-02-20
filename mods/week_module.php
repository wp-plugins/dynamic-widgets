<?php
/**
 * Week Module
 *
 * @version $Id$
 * @copyright 2012 Jacco Drabbe
 */

	defined('ABSPATH') or die("No script kiddies please!");

	class DW_Week extends DWModule {
		protected static $info = 'Beware of double rules!';
		protected static $except = 'Except the weeks';
		public static $option = array( 'week'	=> 'Weeks' );
		protected static $overrule = TRUE;
		protected static $question = 'Show widget on every week?';
		protected static $type = 'complex';

		public static function admin() {
			parent::admin();

			for ( $i = 1; $i < 53; $i++ ) {
				$weeks[$i] = 'Week ' . $i;
			}

			self::mkGUI(self::$type, self::$option[self::$name], self::$question, self::$info, self::$except, $weeks);
		}
	}
?>