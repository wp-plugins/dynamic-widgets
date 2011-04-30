<?php

/**
 *
 *
 * @version $Id$
 * @copyright 2011 Jacco Drabbe
 */

	$tpl = get_page_templates();
	$num_tpl = count($tpl);

	if ( $num_tpl > 0 ) {
		if ( $num_tpl > DW_LIST_LIMIT  ) {
			$tpl_condition_select_style = DW_LIST_STYLE;
		}

		$tpl_yes_selected = 'checked="checked"';
		$opt_tpl = $DW->getOptions($_GET['id'], 'tpl');
		if ( count($opt_tpl) > 0 ) {
			$tpl_act = array();
			foreach ( $opt_tpl as $tpl_condition ) {
				if ( $tpl_condition['name'] == 'default' || empty($tpl_condition['name']) ) {
					$tpl_default = $tpl_condition['value'];
				} else {
					$tpl_act[ ] = $tpl_condition['name'];
				}
			}

			if ( $tpl_default == '0' ) {
				$tpl_no_selected = $tpl_yes_selected;
				unset($tpl_yes_selected);
			}
		}
?>

<h4><b><?php _e('Templates'); ?></b><?php echo ( count($opt_tpl) > 0 ? ' <img src="' . $DW->plugin_url . 'img/checkmark.gif" alt="Checkmark" />' : '' ); ?></h4>
<div class="dynwid_conf">
<?php _e('Show widget on every template?', DW_L10N_DOMAIN); ?> <img src="<?php echo $DW->plugin_url; ?>img/info.gif" alt="info" title="<?php _e('Click to toggle info', DW_L10N_DOMAIN) ?>" onclick="divToggle('tpl');" /><br />
<?php $DW->dumpOpt($opt_tpl); ?>
<div>
	<div id="tpl" class="infotext">
  <?php _e('This options takes precedence above other options like Pages and/or Single Posts.', DW_L10N_DOMAIN); ?>
	</div>
</div>
<input type="radio" name="tpl" value="yes" id="tpl-yes" <?php echo ( isset($tpl_yes_selected) ? $tpl_yes_selected : '' ); ?> /> <label for="tpl-yes"><?php _e('Yes'); ?></label>
<input type="radio" name="tpl" value="no" id="tpl-no" <?php echo ( isset($tpl_no_selected) ? $tpl_no_selected : '' ); ?> /> <label for="tpl-no"><?php _e('No'); ?></label><br />
<?php _e('Except the templates', DW_L10N_DOMAIN); ?>:<br />
<div id="tpl-select" class="condition-select" <?php echo ( isset($tpl_condition_select_style) ? $tpl_condition_select_style : '' ); ?>>
<input type="checkbox" id="tpl_act_page.php" name="tpl_act[]" value="page.php" <?php echo ( isset($tpl_act) && count($tpl_act) > 0 && in_array('page.php', $tpl_act) ) ? 'checked="checked"' : ''; ?> /> <label for="tpl_act_page.php"><?php _e('Default Template'); ?></label><br />
<?php foreach ( $tpl as $tplname => $tplfile ) { ?>
<input type="checkbox" id="tpl_act_<?php echo basename($tplfile); ?>" name="tpl_act[]" value="<?php echo basename($tplfile); ?>" <?php echo ( isset($tpl_act) && count($tpl_act) > 0 && in_array(basename($tplfile), $tpl_act) ) ? 'checked="checked"' : ''; ?> /> <label for="tpl_act_<?php echo basename($tplfile); ?>"><?php echo $tplname; ?></label><br />
<?php } ?>
</div>
</div><!-- end dynwid_conf -->

<?php }  // $num_tpl > 0 ?>