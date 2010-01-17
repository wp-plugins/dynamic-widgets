<?php
/**
 * dynwid_admin.php - Startpage for admin
 *
 * @version $Id$
 */

  require_once('dynwid_class.php');
  if (! isset($DW) ) {
    $DW = new dynWid();
  }
?>

<div class="wrap">
<h2>Dynamic Widgets</h2>

<?php
  // Actions
  switch ( $_GET['action'] ) {
    case 'edit':
      require_once('dynwid_admin_edit.php');
      break;

default:
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
<?php } ?>

<style type="text/css">
.helpbox {
  -moz-border-radius-topleft : 6px;
  -moz-border-radius-topright : 6px;
  -moz-border-radius-bottomleft : 6px;
  -moz-border-radius-bottomright : 6px;
  border-style : solid;
  border-width : 1px;
  border-color : #E3E3E3;
  padding : 5px;
  background-color : white;
  width : 98%;
}
</style>

<div class="helpbox">
<strong>Static / Dynamic</strong><br />
When a widget is <em>Static</em>, the widget uses the WordPress default. In other words, it's shown everywhere.<br />
A widget is <em>Dynamic</em> when there are options set. E.g. not showing on the front page.<br />
<br />

<strong>Reset</strong><br />
Reset makes the widget return to <em>Static</em>.<br />
</div>

<?php
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
  <?php foreach ( $widgets as $widget_id ) { ?>
  <tr>
    <td class="name">
      <p class="row-title"><?php echo $DW->getName($widget_id); ?></p>
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
  <?php } // END foreach $widgets ?>
  </tbody>
 </table>
 </div>
<?php
    } // END count $widgets
  } // END foreach $sidebars
?>

<div class="clear"><br /><br /></div>

<a href="#" onclick="document.getElementById('un').style.display='inline'; return false;">Advanced &gt;</a>
<div id="un" style="display:none">
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

If you deceide not to use this plugin anymore (sorry to hear that!). You can cleanup all settings and data related to this plugin by clicking on the 'Uninstall' button. This process is irreversible! After the cleanup, you can deactivate the plugin.
<br /><br />
<div id="uninstall"> 
  <form action="" method="get">
    <input type="hidden" name="action" value="dynwid_uninstall" />
    <input class="button-primary" type="submit" value="Uninstall" onclick="if ( confirm('Are you sure you want to uninstall Dynamic Widgets?') ) { return true; } return false;" />
  </form>
</div>
</div>

<?php } // END switch ?>

</div> <!-- /wrap //-->