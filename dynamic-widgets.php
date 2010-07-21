<?php
/**
 * Plugin Name: Dynamic Widgets
 * Plugin URI: http://www.qurl.nl/dynamic-widgets/
 * Description: Dynamic Widgets gives you more control over your widgets. It lets you dynamicly place widgets on pages by excluding or including rules by roles, dates, for the homepage, single posts, pages, authors, categories, archives, error page, search page and custom post types.
 * Author: Jacco
 * Version: 1.3.3
 * Author URI: http://www.qurl.nl/
 * Tags: widget, widgets, dynamic, sidebar, custom, rules, admin, conditional tags
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

/*
   WPML Plugin support via API
   Using functions  wpml_get_default_language() > dynwid_worker.php
                    wpml_get_current_language() > dynwid_worker.php, dynwid_class.php, dynwid_class_php4.php
                    wpml_get_content_translation() > dynwid_class.php, dynwid_class_php4.php
*/

  // Constants
  define('DW_DEBUG', FALSE);
  define('DW_DB_TABLE', 'dynamic_widgets');
  define('DW_LIST_LIMIT', 20);
  define('DW_LIST_STYLE', 'style="overflow:auto;height:240px;"');
  define('DW_VERSION', '1.3.3');
  define('DW_VERSION_URL_CHECK', 'http://www.qurl.nl/wp-content/uploads/php/dw_version.php?v=' . DW_VERSION . '&n=');
	define('DW_WPML_API', '/inc/wpml-api.php');			// WPML Plugin support - API file relative to ICL_PLUGIN_PATH
	define('DW_WPML_ICON', 'img/wpml_icon.png');	// WPML Plugin support - WPML icon

	// Class version to use
  if ( version_compare(PHP_VERSION, '5.0.0', '<') ) {
    define('DW_CLASSFILE', 'dynwid_class_php4.php');
  } else {
    define('DW_CLASSFILE', 'dynwid_class.php');
  }
  require_once(dirname(__FILE__) . '/' . DW_CLASSFILE);

  // Functions
	function dynwid_activate() {
		$wpdb = $GLOBALS['wpdb'];
		$dbtable = $wpdb->prefix . DW_DB_TABLE;

		$query = "CREATE TABLE IF NOT EXISTS " . $dbtable . " (
                id int(11) NOT NULL auto_increment,
                widget_id varchar(40) NOT NULL,
                maintype varchar(20) NOT NULL,
                `name` varchar(40) NOT NULL,
                `value` longtext NOT NULL,
              PRIMARY KEY  (id),
              KEY widget_id (widget_id,maintype)
            );";
		$wpdb->query($query);

		// Version check
		$version = get_option('dynwid_version');
		if ( $version !== FALSE ) {
/*    1.2 > Added support for widget display setting options for Author Pages.
   Need to apply archive rule to author also to keep same behavior. */
			if ( version_compare($version, '1.2', '<') ) {
				$query = "SELECT widget_id FROM " . $dbtable . " WHERE maintype = 'archive'";
				$results = $wpdb->get_results($query);
				foreach ( $results as $myrow ) {
					$query = "INSERT INTO " .$dbtable . "(widget_id, maintype, value) VALUES ('" . $myrow->widget_id . "', 'author', '0')";
					$wpdb->query($query);
				}
			}

/*    1.3 > Added Date (range) support.
   Need to change DB `value` to a LONGTEXT type
   (not for the date of course, but for supporting next features which might need a lot of space) */
			if ( version_compare($version, '1.3', '<') ) {
				$query = "ALTER TABLE " . $dbtable . " CHANGE `value` `value` LONGTEXT NOT NULL";
				$wpdb->query($query);
			}
		}
		update_option('dynwid_version', DW_VERSION);
	}

  function dynwid_add_admin_menu() {
    $DW = &$GLOBALS['DW'];

    $screen = add_submenu_page('themes.php', 'Dynamic Widgets', 'Dynamic Widgets', 'switch_themes', 'dynwid-config', 'dynwid_admin_page');
    add_action('admin_print_styles-' . $screen, 'dynwid_add_admin_styles');
    add_action('admin_print_scripts-' . $screen, 'dynwid_add_admin_scripts');

    // Contextual help
    if ( $_GET['action'] == 'edit' ) {
      $help  = 'Widgets are always displayed by default (The \'<em>Yes</em>\' selection).<br />';
      $help .= 'Click on the <img src="' . $DW->plugin_url . 'img/info.gif" alt="info" /> next to the options for more info.';
    } else {
      $help  = '<p><strong>Static / Dynamic</strong><br />';
      $help .= 'When a widget is <em>Static</em>, the widget uses the WordPress default. In other words, it\'s shown everywhere.<br />';
      $help .= 'A widget is <em>Dynamic</em> when there are options set, i.e. not showing on the front page.</p>';
      $help .= '<p><strong>Reset</strong><br />';
      $help .= 'Reset makes the widget return to <em>Static</em>.</p>';
    }
    add_contextual_help($screen, $help);

    // Only show meta box in posts panel when there are widgets enabled.
    $opt = $DW->getOptions('%','individual');
    if ( count($opt) > 0 ) {
      add_meta_box('dynwid', 'Dynamic Widgets', 'dynwid_add_post_control', 'post', 'side', 'low');
    }
  }

  function dynwid_add_admin_scripts() {
  	$DW = &$GLOBALS['DW'];
    wp_enqueue_script('jquery');
    wp_enqueue_script('jquery-ui-core');
    wp_enqueue_script('jquery-ui-datepicker', $DW->plugin_url . 'jquery_datepicker.js', array('jquery-ui-core'));
  }

  function dynwid_add_admin_styles() {
    $DW = &$GLOBALS['DW'];
    wp_enqueue_style('jquery-ui-smoothness', $DW->plugin_url . 'jquery-ui-smoothness.css');
  }

  function dynwid_add_plugin_actions($all) {
    $links = array();
	  $links[ ] = '<a href="themes.php?page=dynwid-config">' . __('Settings') . '</a>';

    return array_merge($links, $all);
  }

  function dynwid_add_post_control() {
    $post = $GLOBALS['post'];
    $DW = &$GLOBALS['DW'];

    $opt = $DW->getOptions('%','individual');
    echo '<strong>Apply exception rule to widgets:</strong><br /><br />';
    foreach ( $opt as $widget ) {
      $single_condition = '1';
      $checked = '';
      $opt_single = $DW->getOptions($widget['widget_id'], 'single');

      // loop through the opts to see if we have a match
      foreach ( $opt_single as $widget_opt ) {
        if ( $widget_opt['maintype'] == 'single' ) {
          $single_condition = $widget_opt['value'];
        }
        if ( $widget_opt['maintype'] == 'single-post' && $widget_opt['name'] == $post->ID ) {
          $checked = ' checked="checked"';
        }
      }

      $default = ( $single_condition == '0' ) ? 'Off' : 'On';
      echo '<input type="checkbox" id="dw_' . $widget['widget_id'] . '" name="dw-single-post[]" value="' . $widget['widget_id'] . '"' . $checked . ' /> <label for="dw_' . $widget['widget_id'] . '">' . $DW->getName($widget['widget_id']) . ' (Default: ' . $default . ')</label><br />';
    }
  }

  function dynwid_add_tag_page() {
    $DW = &$GLOBALS['DW'];

    // Only show dynwid row when there are widgets enabled
    $opt = $DW->getOptions('%','individual');
    if ( count($opt) > 0 ) {

      echo '<tr class="form-field">';
      echo '<th scope="row" valign="top"><label for="dynamic-widgets">Dynamic Widgets</label></th>';
      echo '<td>';
      foreach ( $opt as $widget ) {
        $single_condition = '1';
        $checked = '';
        $opt_single = $DW->getOptions($widget['widget_id'], 'single');

        // loop through the opts to see if we have a match
        foreach ( $opt_single as $widget_opt ) {
          if ( $widget_opt['maintype'] == 'single' ) {
            $single_condition = $widget_opt['value'];
          }
          if ( $widget_opt['maintype'] == 'single-tag' && $widget_opt['name'] == $_GET['tag_ID'] ) {
            $checked = ' checked="checked"';
          }
        }

        $default = ( $single_condition == '0' ) ? 'Off' : 'On';
        echo '<input type="checkbox" style="width:10pt;border:none;" id="dw_' . $widget['widget_id'] . '" name="dw-single-tag[]" value="' . $widget['widget_id'] . '"' . $checked . ' /> <label for="dw_' . $widget['widget_id'] . '">' . $DW->getName($widget['widget_id']) . ' (Default: ' . $default . ')</label><br />';

      } // END foreach opt
      echo '</td>';
      echo '</tr>';
    }
  }

  function dynwid_add_widget_control() {
    $DW = &$GLOBALS['DW'];

    /*
      Hooking into the callback of the widgets by moving the existing callback to wp_callback
      and setting callback with own callback function.
      We need the widget_id registered in params also for calling own callback.
    */
    foreach ( $DW->registered_widgets as $widget_id => $widget ) {
      if ( array_key_exists($widget_id, $DW->registered_widget_controls) ) {
        $DW->registered_widget_controls[$widget_id]['wp_callback'] = $DW->registered_widget_controls[$widget_id]['callback'];
        $DW->registered_widget_controls[$widget_id]['callback'] = 'dynwid_widget_callback';

        /*
          In odd cases params and/or params[0] seems not to be an array. Bugfix for:
          Warning: Cannot use a scalar value as an array in ./wp-content/plugins/dynamic-widgets/dynamic-widgets.php on line 150
          If the bug is not fixed, warning should now be on line 173
        */

        /* Fixing params */
        if (! is_array($DW->registered_widget_controls[$widget_id]['params']) ) {
          $DW->registered_widget_controls[$widget_id]['params'] = array();
        }

        if ( count($DW->registered_widget_controls[$widget_id]['params']) == 0 ) {
          $DW->registered_widget_controls[$widget_id]['params'][ ] = array('widget_id' => $widget_id);
        /* Fixing params[0] */
        } else if (! is_array($DW->registered_widget_controls[$widget_id]['params'][0]) ) {
          $DW->registered_widget_controls[$widget_id]['params'][0] = array('widget_id' => $widget_id);
        } else {
          $DW->registered_widget_controls[$widget_id]['params'][0]['widget_id'] = $widget_id;
        }
      }
    }

    // Notifying user when options are saved and returned to ./wp-admin/widgets.php
    if ( $_GET['dynwid_save'] == 'yes' ) {
      add_action('sidebar_admin_page', 'dynwid_add_widget_page');
    }
  }

  function dynwid_add_widget_page() {
    $DW = &$GLOBALS['DW'];

    $name = strip_tags($DW->getName($_GET['widget_id']));

    echo '<div class="updated fade" id="message">';
    echo '<p>';
    echo '<strong>Dynamic Widgets Options saved</strong> for ' . $name;
    echo '</p>';
    echo '</div>';
  }

  function dynwid_admin_dump() {
    $DW = &$GLOBALS['DW'];

    header('Content-Description: File Transfer');
    header('Content-Disposition: attachment; filename=dynwid_dump_' . date('Ymd') . '.txt' );
    header('Content-Type: text/plain');

    $DW->dump();
    die();
  }

  function dynwid_admin_page() {
    $DW = &$GLOBALS['DW'];
    require_once(dirname(__FILE__) . '/dynwid_admin.php');
  }

  function dynwid_check_version($plugin_data, $r) {
    $check = wp_remote_fopen(DW_VERSION_URL_CHECK . $r->new_version);

    if ( $check && ! empty($check) ) {
      echo '<div style="font-weight:normal;">';
      echo $check;
      echo '</div>';
    }
  }

  function dynwid_init() {
    $GLOBALS['DW'] = new dynWid();

  	if ( is_admin() ) {
  	  if ( $_POST['dynwid_save'] == 'yes' ) {
  	    $DW = &$GLOBALS['DW'];
  	    require_once(dirname(__FILE__) . '/dynwid_admin_save.php');
  	  }

			add_action('admin_menu', 'dynwid_add_admin_menu');
  	  add_action('edit_tag_form_fields', 'dynwid_add_tag_page');
  	  add_action('edited_term', 'dynwid_save_tagdata');
  	  add_action('in_plugin_update_message-' . plugin_basename(__FILE__), 'dynwid_check_version', 10, 2);
			add_action('plugin_action_links_' . plugin_basename(__FILE__), 'dynwid_add_plugin_actions');
  	  add_action('save_post', 'dynwid_save_postdata');
  	  add_action('sidebar_admin_setup', 'dynwid_add_widget_control');
		} else {
			add_action('wp_head', 'dynwid_worker');
		}
  }

	function dynwid_install() {
		if ( function_exists('is_multisite') ) {
			if ( is_multisite() && $_GET['networkwide'] == '1' ) {
				$plugin = plugin_basename(__FILE__);
				deactivate_plugins($plugin);
			} else {
				dynwid_activate();
			}
		} else {
			dynwid_activate();
		}
	}

	function dynwid_save_postdata($post_id) {
	  $DW = &$GLOBALS['DW'];

    // Using parent post_id to prevent cluttering up the database with revision numbers
	  if ( array_key_exists('post_ID', $_POST) ) {
	    $post_id = $_POST['post_ID'];
	  }

	  // Housekeeping
	  $opt = $DW->getOptions('%','individual');
	  foreach ( $opt as $widget ) {
	    $DW->deleteOption($widget['widget_id'], 'single-post', $post_id);
	  }

	  if ( array_key_exists('dw-single-post', $_POST) ) {
	    $opt = $_POST['dw-single-post'];
	    $default = 'yes';
	    $default_single = '1';

	    foreach ( $opt as $widget_id ) {
	      $opt_single = $DW->getOptions($widget_id, 'single');
	      if ( count($opt_single) > 0 ) {
	        foreach ( $opt_single as $widget ) {
	          if ( $widget['maintype'] == 'single' ) {
	            $default_single = $widget['value'];
	          }
	        }

	        if ( $default_single == '0' ) {
	          $default = 'no';
	        }
	      }
	      $DW->addMultiOption($widget_id, 'single-post', $default, array($post_id));
	    }
	  } // END if array_key_exists
	}

	function dynwid_save_tagdata($term_id) {
	  // Only act when tag is updated via 'edit', NOT via 'quick edit'
	  if ( $_POST['action'] == 'editedtag' ) {
	    $DW = &$GLOBALS['DW'];

	    if ( array_key_exists('tag_ID', $_POST) ) {
	      $term_id = $_POST['tag_ID'];
	    }

	    // Housekeeping
	    $opt = $DW->getOptions('%','individual');
	    foreach ( $opt as $widget ) {
	      $DW->deleteOption($widget['widget_id'], 'single-tag', $term_id);
	    }

	    if ( array_key_exists('dw-single-tag', $_POST) ) {
	      $opt = $_POST['dw-single-tag'];
	      $default = 'yes';
	      $default_single = '1';

	      foreach ( $opt as $widget_id ) {
	        $opt_single = $DW->getOptions($widget_id, 'single');
	        if ( count($opt_single) > 0 ) {
	          foreach ( $opt_single as $widget ) {
	            if ( $widget['maintype'] == 'single' ) {
	              $default_single = $widget['value'];
	            }
	          }
	        }
	        $DW->addMultiOption($widget_id, 'single-tag', $default, array($term_id));
	      }
	    } // END if array_key_exists
	  } // END if action
	}

	function dynwid_uninstall() {
		$wpdb = $GLOBALS['wpdb'];
	  $dbtable = $wpdb->prefix . DW_DB_TABLE;

    // Housekeeping
		delete_option('dynwid_version');
		$query = "DROP TABLE IF EXISTS " . $dbtable;
		$wpdb->query($query);

	  $plugin = plugin_basename(__FILE__);

	  /* Shamelessly ripped from /wp-admin/plugins.php */
    deactivate_plugins($plugin);
	  update_option('recently_activated', array($plugin => time()) + (array) get_option('recently_activated'));
	  wp_redirect('plugins.php?deactivate=true&plugin_status=' . $status . '&paged=' . $page);

    die();
	}

	function dynwid_widget_callback() {
	  $DW = &$GLOBALS['DW'];

	  $args = func_get_args();
	  $widget_id = $args[0]['widget_id'];
	  $wp_callback = $DW->registered_widget_controls[$widget_id]['wp_callback'];

	  // Calling original callback first
    call_user_func_array($wp_callback, $args);

	  // Now adding the dynwid text & link
	  echo '<p><b>Dynamic Widgets</b><br />';
	  echo 'This widget is <a title="Edit Dynamic Widgets Options" href="themes.php?page=dynwid-config&action=edit&id=' . $widget_id . '&returnurl=' . urlencode($_SERVER['REQUEST_URI']) . '">';
	  echo ( $DW->hasOptions($widget_id) ) ? 'dynamic' : 'static';
	  echo '</a>.';
	  if ( $DW->hasOptions($widget_id) ) {
	    $s = array();
	    $buffer = array(
	                'role'        => 'Role',
	                'date'        => 'Date',
	                'front-page'  => 'Front Page',
	                'single'      => 'Single Posts',
	                'page'        => 'Pages',
	                'author'      => 'Author Pages',
	                'category'    => 'Category Pages',
	                'archive'     => 'Archive Pages',
	                'e404'        => 'Error Page',
	                'search'      => 'Search page'
	              );

      // Adding Custom Post Types to $buffer
	    if ( version_compare($GLOBALS['wp_version'], '3.0', '>=') ) {
	      $args = array(
	                'public'   => TRUE,
	                '_builtin' => FALSE
	              );
	      $post_types = get_post_types($args, 'objects', 'and');
	      foreach ( $post_types as $ctid ) {
	        $buffer[key($post_types)] = $ctid->label;
	      }
      }

	    $opt = $DW->getOptions($widget_id, NULL);
	    foreach ( $opt as $widget ) {
	      $type = $widget['maintype'];
	      if ( $type != 'individual' ) {
	        $single = array('single-author', 'single-category', 'single-tag', 'single-post');
	        if ( in_array($type, $single) ) {
	          $type = 'single';
	        }
	        if (! in_array($type, $s) ) {
	          $s[ ] = $type;
	        }
	      }
	    }

	    $last = count($s) - 1;
	    for ( $i = 0; $i < $last; $i++ ) {
	      $type = $s[$i];
	      if (! empty($buffer[$type]) ) {
	        $string .= $buffer[$type];
	      }
	      $string .= ( ($last - 1) == $i ) ? ' and ' : ', ';
	    }
	    $type = $s[$last];
	    $string .= $buffer[$type];

	    $output  = '<br /><small>Option';
	    $output .= ( count($opt) > 1 ) ? 's' : '';
      $output .= ' set for ' . $string . '.</small>';
	    echo $output;
	  }
	  echo '</p>';
	}

	function dynwid_worker() {
	  $DW = &$GLOBALS['DW'];
	  require_once(dirname(__FILE__) . '/dynwid_worker.php');
	}

  // Hooks
  add_action('admin_action_dynwid_dump', 'dynwid_admin_dump');
  add_action('admin_action_dynwid_uninstall', 'dynwid_uninstall');
  add_action('init', 'dynwid_init');
  register_activation_hook(__FILE__, 'dynwid_install');
?>