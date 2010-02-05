<?php
/**
 * dynwid_admin_dump.php
 *
 * @version $Id$
 */

  require_once('dynwid_class.php');
  if (! isset($DW) ) {
    $DW = new dynWid();
  }

	header('Content-Description: File Transfer');
	header('Content-Disposition: attachment; filename=dynwid_dump_' . date('Ymd') . '.txt' );
	header('Content-Type: text/plain');

	$DW->dump();
?>