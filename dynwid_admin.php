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
  // Actions
  switch ( $_GET['action'] ) {
    case 'edit':
      require_once(dirname(__FILE__) . '/dynwid_admin_edit.php');
      break;

    default:
      require_once(dirname(__FILE__) . '/dynwid_admin_overview.php');
  }
?>

<!-- Footer //-->
<div class="clear"><br /><br /></div>
<div><small>
  <a href="http://www.qurl.nl/dynamic-widgets/" target="_blank">Dynamic Widgets</a> v<?php echo DW_VERSION; ?> (<?php echo DW_CLASSFILE; ?>)
</small></div>

</div> <!-- /wrap //-->