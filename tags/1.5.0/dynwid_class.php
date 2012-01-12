<?php
/**
 * dynwid_class.php - Dynamic Widgets Classes loader (PHP5)
 *
 * @version $Id$
 * @copyright 2011 Jacco Drabbe
 */

	$dh = opendir(DW_CLASSES);
	while ( ($file = readdir($dh)) !== FALSE ) {
		if ( $file != '.' && $file != '..' && substr(strrchr($file, '_'), 1) == 'class.php' ) {
				include_once(DW_CLASSES . $file);
		}
	}
?>
