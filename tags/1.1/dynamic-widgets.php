<?php
/**
 * Plugin Name: Dynamic Widgets
 * Plugin URI: http://www.qurl.nl/2010/02/dynamic-widgets-1-1/ 
 * Description: Dynamic Widgets gives you more control over your widgets. It lets you dynamicly place widgets on pages by excluding or including rules by roles, for the homepage, single posts, pages, categories, archives and the error 404 page.
 * Author: Jacco
 * Version: 1.1
 * Author URI: http://www.qurl.nl/
 * Tags: widget, widgets, dynamic, sidebar, custom, rules
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * Released under the GPL v.2, http://www.gnu.org/copyleft/gpl.html
 *
 * @version $Id$
 */

  // Constants
  define('DW_DB_TABLE', 'dynamic_widgets');
  define('DW_LIST_LIMIT', 20);
  define('DW_LIST_STYLE', 'style="overflow:auto;height:240px;"');
  define('DW_VERSION', '1.1');
  define('DEBUG', FALSE);

  // Functions
  function dynwid_add_admin_menu() {
    add_submenu_page('themes.php', 'Dynamic Widgets', 'Dynamic Widgets', 'switch_themes', 'dynwid-config', 'dynwid_admin_page');
  }

  function dynwid_add_plugin_actions($all) {
    $links = array();
	  $links[ ] = '<a href="themes.php?page=dynwid-config">' . __('Settings') . '</a>';

    return array_merge($links, $all);
  }

  function dynwid_admin_dump(){
    require_once('dynwid_admin_dump.php');
    die();
  }

  function dynwid_admin_page() {
    require_once('dynwid_admin.php');
  }

  function dynwid_init() {
  	if ( is_admin() ) {
  	  global $_POST, $wpdb;
  	  if ( $_POST['dynwid_save'] == 'yes' ) {
  	    require_once('dynwid_admin_save.php');
  	  }

			add_action('admin_menu', 'dynwid_add_admin_menu');
			add_action('plugin_action_links_' . plugin_basename(__FILE__), 'dynwid_add_plugin_actions');
		} else {
			add_action('wp_head', 'dynwid_worker');
		}
  }

	function dynwid_install() {
		global $wpdb;
	  $dbtable = $wpdb->prefix . DW_DB_TABLE;

	  $query = "CREATE TABLE IF NOT EXISTS " . $dbtable . " (
                id int(11) NOT NULL auto_increment,
                widget_id varchar(40) NOT NULL,
                maintype varchar(20) NOT NULL,
                `name` varchar(40) NOT NULL,
                `value` smallint(1) NOT NULL default '1',
                PRIMARY KEY  (id),
                KEY widget_id (widget_id,maintype)
              );";
    $wpdb->query($query);

	  // Version check
	  /* $version = get_option('dynwid_version');
	  if ( version_compare($version, DW_VERSION, '<') ) {

	  } */
		update_option('dynwid_version', DW_VERSION);
	}

	function dynwid_uninstall() {
		global $wpdb;
	  $dbtable = $wpdb->prefix . DW_DB_TABLE;

    // Housekeeping
		delete_option('dynwid_version');
		$query = "DROP TABLE " . $dbtable;
		$wpdb->query($query);

    // Redirect to plugins page for deactivation
		wp_redirect( get_option('siteurl') . '/wp-admin/plugins.php' );
		die();
	}

	function dynwid_worker() {
	  require_once('dynwid_worker.php');
	}

  // Hooks
  add_action('admin_action_dynwid_dump', 'dynwid_admin_dump');
  add_action('admin_action_dynwid_uninstall', 'dynwid_uninstall');
  add_action('init', 'dynwid_init');
  register_activation_hook(__FILE__, 'dynwid_install');
?>