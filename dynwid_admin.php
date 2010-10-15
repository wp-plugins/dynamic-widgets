<?php
/**
 * dynwid_admin.php - Startpage for admin
 *
 * @version $Id$
 */
?>

<div class="wrap">
<h2>Dynamic Widgets</h2>

<?php
	if ( $DW->enabled ) {
		// Actions
		if ( isset($_GET['action']) && $_GET['action'] == 'edit' ) {
			$dw_admin_script = '/dynwid_admin_edit.php';
		} else {
			$dw_admin_script = '/dynwid_admin_overview.php';

			// Do some housekeeping
			$lastrun = get_option('dynwid_housekeeping_lastrun');
			if ( time() - $lastrun > DW_TIME_LIMIT ) {
				$DW->housekeeping();
				update_option('dynwid_housekeeping_lastrun', time());
			}
		}
		require_once(dirname(__FILE__) . $dw_admin_script);
	} else {
		echo '<div class="error" id="message"><p>';
		echo 'Oops! Something went terrible wrong. Please reinstall Dynamic Widgets.';
		echo '</p></div>';
	}
?>

<!-- Footer //-->
<div class="clear"><br /><br /></div>
<div><small>
  <a href="<?php echo DW_URL; ?>/dynamic-widgets/" target="_blank">Dynamic Widgets</a> v<?php echo DW_VERSION; ?> (<?php echo ( DW_CLASSFILE == 'dynwid_class_php4.php' ? 'PHP4' : 'PHP5' ) . ', ' . ( DW_OLD_METHOD ? 'OLD'  : 'FILTER' ); ?>)
</small></div>

</div> <!-- /wrap //-->