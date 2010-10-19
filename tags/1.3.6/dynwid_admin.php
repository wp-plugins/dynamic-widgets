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
		if ( dynwid_sql_mode() ) {
			echo '<div class="error" id="message"><p>';
			_e('<b>WARNING</b> STRICT sql mode in effect. Dynamic Widgets might not work correctly. Please disable STRICT sql mode.', DW_L10N_DOMAIN);
			echo '</p></div>';
		}

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
		_e('Oops! Something went terrible wrong. Please reinstall Dynamic Widgets.', DW_L10N_DOMAIN);
		echo '</p></div>';
	}
?>

<!-- Footer //-->
<div class="clear"><br /><br /></div>
<div><small>
  <a href="<?php echo DW_URL; ?>/dynamic-widgets/" target="_blank">Dynamic Widgets</a> v<?php echo DW_VERSION; ?> (<?php echo ( DW_CLASSFILE == 'dynwid_class_php4.php' ? 'PHP4' : 'PHP5' ) . ', ' . ( DW_OLD_METHOD ? __('OLD', DW_L10N_DOMAIN)  : __('FILTER', DW_L10N_DOMAIN) ); ?>)
</small></div>

</div> <!-- /wrap //-->