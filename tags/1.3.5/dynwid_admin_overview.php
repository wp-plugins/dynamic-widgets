<?php
/**
 * dynwid_admin_overview.php - Overview page
 *
 * @version $Id$
 */

  // Special case: Reset action needs to go back to overview.
  if ( $_GET['action'] == 'reset' ) {
    check_admin_referer('plugin-name-action_reset_' . $_GET['id']);
    $DW->resetOptions($_GET['id']);
?>
<div class="updated fade" id="message">
  <p>
    <strong>Widget options have been reset to default.</strong><br />
  </p>
</div>
<?php
  }
  if ( $_GET['action'] == 'dynwid_set_method' ) {
  	if ( $_GET['oldmethod'] == 'on' ) {
  		update_option('dynwid_old_method', TRUE);
  	} else {
  		update_option('dynwid_old_method', FALSE);
  	}
?>
<div class="updated fade" id="message">
	<p><strong>Method set to <?php echo ( get_option('dynwid_old_method') ? '\'OLD\'' : '\'FILTER\'' ); ?>.</strong></p>
</div>
<?php
  }

  foreach ( $DW->sidebars as $sidebar_id => $widgets ) {
    if ( count($widgets) > 0 ) {
      if ( $sidebar_id == 'wp_inactive_widgets' ) {
        $name = 'Inactive Widgets';
      } else {
        $name = $DW->getName($sidebar_id, 'S');
      }
?>

<div class="postbox-container" style="width:48%;margin-top:10px;margin-right:10px;">
<table cellspacing="0" class="widefat fixed">
	<thead>
	<tr>
	  <th class="managage-column" scope="col"><?php echo $name; ?></th>
	  <th style="width:70px">&nbsp;</th>
  </tr>
  </thead>

  <tbody class="list:link-cat" id="<?php echo str_replace('-', '_', $sidebar_id); ?>">
  <?php foreach ( $widgets as $widget_id ) {
          $name = $DW->getName($widget_id);
          // When $name is empty, we have a widget which not belongs here
          if (! empty($name) ) {
  ?>
  <tr>
    <td class="name">
      <p class="row-title"><a title="Edit this widget options" href="themes.php?page=dynwid-config&amp;action=edit&amp;id=<?php echo $widget_id; ?>"><?php echo $name; ?></a></p>
      <div class="row-actions">
       <span class="edit">
          <a title="Edit this widget options" href="themes.php?page=dynwid-config&amp;action=edit&amp;id=<?php echo $widget_id; ?>">Edit</a>
        </span>
        <?php if ( $DW->hasOptions($widget_id) ) { ?>
        <span class="delete">
        <?php $href = wp_nonce_url('themes.php?page=dynwid-config&amp;action=reset&amp;id=' . $widget_id, 'plugin-name-action_reset_' . $widget_id); ?>
          | <a class="submitdelete" title="Reset widget to Static" onclick="if ( confirm('You are about to reset this widget \'<?php echo strip_tags($DW->getName($widget_id)); ?>\'\n \'Cancel\' to stop, \'OK\' to reset.') ) { return true;}return false;" href="<?php echo $href; ?>">Reset</a>
        </span>
        <?php } ?>
      </div>
    </td>
    <td>
      <?php echo ( $DW->hasOptions($widget_id) ) ? 'Dynamic' : 'Static'; ?>
    </td>
  </tr>
  <?php   } // END if (! empty($name) ) ?>
  <?php } // END foreach ( $widgets as $widget_id ) ?>
  </tbody>
 </table>
 </div>
<?php
    } // END if ( count($widgets) > 0 )
  } // END foreach ( $DW->sidebars as $sidebar_id => $widgets )
?>

<div class="clear"><br /><br /></div>

<a href="#" onclick="jQuery('#un').slideToggle('fast'); return false;">Advanced &gt;</a>
<div id="un" style="display:none">
<br /> <strong>wp_head() check: </strong>
<?php
  $c = $DW->checkWPhead();
  switch ( $c ) {
    case 0:
      echo '<span style="color:red">wp_head() is NOT called (at the most obvious place)</span>';
      break;

    case 1:
      echo '<span style="color:green">wp_head() is called</span>';
      break;

    case 2:
      echo '<span style="color:orange">Unable to determine if wp_head() is called</span>';
      break;
  }
?>
.<br />

<br />
<div id="method">
	<form id="dynwid_method" action="" method="get">
		<input type="hidden" name="page" value="dynwid-config" />
		<input type="hidden" name="action" value="dynwid_set_method" />
		<input type="checkbox" id="oldmethod" name="oldmethod" <?php echo ( get_option('dynwid_old_method') ? 'checked="checked"' : '' ) ?> onchange="document.getElementById('dynwid_method').submit();" /> <label for="oldmethod">Use 'OLD' method</label>
</form>
</div>

<br />
For debugging purposes it is possible you're asked to create a dump. Click the 'Create dump' button and save the text file.
<br /><br />
<div id="dump">
  <form action="" method="get">
    <input type="hidden" name="action" value="dynwid_dump" />
    <input class="button-primary" type="submit" value="Create dump" />
  </form>
</div>

<br /><br />

If you deceide not to use this plugin anymore (sorry to hear that!). You can cleanup all settings and data related to this plugin by clicking on the 'Uninstall' button. This process is irreversible! After the cleanup the plugin is deactivated automaticly.
<br /><br />
<div id="uninstall">
  <form action="" method="get">
    <input type="hidden" name="action" value="dynwid_uninstall" />
    <input class="button-primary" type="submit" value="Uninstall" onclick="if ( confirm('Are you sure you want to uninstall Dynamic Widgets?') ) { return true; } return false;" />
  </form>
</div>
</div>