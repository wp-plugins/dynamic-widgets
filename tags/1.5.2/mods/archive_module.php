<?php
/**
 * Archive Module
 *
 * @version $Id$
 * @copyright 2011 Jacco Drabbe
 */

	class DW_Archive extends DWModule {
		protected static $info = 'This option does not include Author and Category Pages.';
		public static $option = array( 'archive' => 'Archive Pages' );
		protected static $question = 'Show widget on archive pages?';
	}
?>