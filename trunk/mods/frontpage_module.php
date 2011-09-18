<?php
/**
 * Front Page Module
 *
 * @version $Id$
 * @copyright 2011 Jacco Drabbe
 */

	class DW_Front_page extends DWModule {
		protected static $info = 'This option only applies when your front page is set to display your latest posts (See Settings &gt; Reading).<br />When a static page is set, you can use the options for the static pages below.';
		public static $option = array( 'front-page' => 'Front Page' );
		protected static $question = 'Show widget on the front page?';
		protected static $type = 'custom';

		public static function admin() {
			parent::admin();

			if ( get_option('show_on_front') != 'page' ) {
				self::mkGUI('simple', self::$option[self::$name], self::$question, self::$info);
			}
		}
	}
?>
