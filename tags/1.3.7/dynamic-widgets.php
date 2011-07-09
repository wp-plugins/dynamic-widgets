<?php
/**
 * Plugin Name: Dynamic Widgets
 * Plugin URI: http://www.qurl.nl/dynamic-widgets/
 * Description: Dynamic Widgets gives you full control on which pages your widgets will appear. It lets you dynamicly place the widgets on WordPress pages.
 * Author: Jacco
 * Version: 1.3.7
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
   Thanks to Alexis Nomine for the contributions of the French (fr_FR) language files, several L10N fixes and change of the edit options UI.
 */

/*
   WPML Plugin support via API
   Using constants	ICL_PLUGIN_PATH > dynwid_admin_edit.php, dynwid_init_worker.php, dynwid_worker.php
   Using functions  wpml_get_default_language() > dynwid_init_worker.php
                    wpml_get_current_language() > dynwid_init_worker.php, dynwid_worker.php, wpml.php
                    wpml_get_content_translation() > wpml.php
   									wpml_get_active_languages() > dynwid_admin_edit.php
 */

/*
	 WPSC/WPEC Plugin support
 	 Using constants	WPSC_TABLE_PRODUCT_CATEGORIES	> dynwid_admin_edit.php, dynwid_init_worker.php, wpsc.php
 	 Using vars 			$wpsc_query > dynwid_init_worker.php, wpsc.php
 */

  // Constants
  define('DW_DEBUG', FALSE);
  define('DW_DB_TABLE', 'dynamic_widgets');
  define('DW_L10N_DOMAIN', 'dynamic-widgets');
  define('DW_LIST_LIMIT', 20);
  define('DW_LIST_STYLE', 'style="overflow:auto;height:240px;"');
  define('DW_OLD_METHOD', get_option('dynwid_old_method'));
  define('DW_PLUGIN', dirname(__FILE__) . '/' . 'plugin/');
  define('DW_TIME_LIMIT', 86400);				// 1 day
  define('DW_URL', 'http://www.qurl.nl');
  define('DW_VERSION', '1.3.7');
  define('DW_VERSION_URL_CHECK', DW_URL . '/wp-content/uploads/php/dw_version.php?v=' . DW_VERSION . '&n=');
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
	/**
	 * dynwid_activate() Activate the plugin
	 * @since 1.3.3
	 */
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
					$query = "INSERT INTO " . $dbtable . "(widget_id, maintype, value) VALUES ('" . $myrow->widget_id . "', 'author', '0')";
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

  /**
   * dynwid_add_admin_menu() Add plugin link to admin menu
   * @since 1.0
   */
  function dynwid_add_admin_menu() {
    $DW = &$GLOBALS['DW'];

    $screen = add_submenu_page('themes.php', 'Dynamic Widgets', 'Dynamic Widgets', 'switch_themes', 'dynwid-config', 'dynwid_admin_page');

  	if ( $DW->enabled ) {
  		add_action('admin_print_styles-' . $screen, 'dynwid_add_admin_styles');
  		add_action('admin_print_scripts-' . $screen, 'dynwid_add_admin_scripts');

  		// Contextual help
  		if ( isset($_GET['action']) && $_GET['action'] == 'edit' ) {
  			$help  = __('Widgets are always displayed by default', DW_L10N_DOMAIN) . ' (' . __('The \'<em>Yes</em>\' selection', DW_L10N_DOMAIN) . ')'  . '<br />';
  			$help .= __('Click on the', DW_L10N_DOMAIN) . ' <img src="' . $DW->plugin_url . 'img/info.gif" alt="info" /> ' . __('next to the options for more info', DW_L10N_DOMAIN) . '.<br />';
  			$help .= __('The') . ' <span class="hasoptions">*</span> ' . __('next to a section means it has options set.', DW_L10N_DOMAIN);
  		} else {
  			$help  = '<p><strong>' . __('Static', DW_L10N_DOMAIN) . ' / ' . __('Dynamic', DW_L10N_DOMAIN) . '</strong><br />';
  			$help .= __('When a widget is', DW_L10N_DOMAIN) . ' <em>' . __('Static', DW_L10N_DOMAIN) . '</em>, ' . __('the widget uses the WordPress default. In other words, it\'s shown everywhere', DW_L10N_DOMAIN) . '.<br />';
  			$help .=  __('A widget is', DW_L10N_DOMAIN) . ' <em>' . __('Dynamic', DW_L10N_DOMAIN) . '</em> ' . __('when there are options set, i.e. not showing on the front page.', DW_L10N_DOMAIN) . '</p>';
  			$help .= '<p><strong>' . __('Reset', DW_L10N_DOMAIN) . '</strong><br />';
  			$help .= __('Reset makes the widget return to', DW_L10N_DOMAIN) . ' <em>' . __('Static', DW_L10N_DOMAIN) . '</em>.</p>';
  		}
  		add_contextual_help($screen, $help);

  		// Only show meta box in posts panel when there are widgets enabled.
  		$opt = $DW->getOptions('%','individual');
  		if ( count($opt) > 0 ) {
  			add_meta_box('dynwid', __('Dynamic Widgets', DW_L10N_DOMAIN), 'dynwid_add_post_control', 'post', 'side', 'low');
  		}
  	}
  }

  /**
   * dynwid_add_admin_scripts() Enqueue jQuery UI scripts to admin page
   * @since 1.3
   */
  function dynwid_add_admin_scripts() {
  	$DW = &$GLOBALS['DW'];
    wp_enqueue_script('jquery');
    wp_enqueue_script('jquery-ui-core');
    wp_enqueue_script('jquery-ui-datepicker', $DW->plugin_url . 'jquery_datepicker.js', array('jquery-ui-core'));
    wp_enqueue_script('jquery-ui-accordion', $DW->plugin_url . 'jquery.ui.accordion.min.js', array('jquery-ui-core'));
  }

  /**
   * dynwid_add_admin_styles() Enqueue CSS to admin page
   * @since 1.3
   */
  function dynwid_add_admin_styles() {
    $DW = &$GLOBALS['DW'];
    wp_enqueue_style('jquery-ui-core', $DW->plugin_url . 'jquery.ui.core.css');
    wp_enqueue_style('jquery-ui-smoothness', $DW->plugin_url . 'jquery.ui.theme.smoothness.css', array('jquery-ui-core'));
    wp_enqueue_style('jquery-ui-accordion', $DW->plugin_url . 'jquery.ui.accordion.css', array('jquery-ui-core', 'jquery-ui-smoothness'));
    wp_enqueue_style('jquery-ui-datepicker', $DW->plugin_url . 'jquery.ui.datepicker.css', array('jquery-ui-core', 'jquery-ui-smoothness'));
  }

  /**
   * dynwid_add_plugin_actions() Add settings link in WP plugin overview
   * @param array $all
   * @return array
   * @since 1.0
   */
  function dynwid_add_plugin_actions($all) {
    $links = array();
	  $links[ ] = '<a href="themes.php?page=dynwid-config">' . __('Settings') . '</a>';

    return array_merge($links, $all);
  }

  /**
   * dynwid_add_post_control() Add control widget to post screen
   * @since 1.2
   */
  function dynwid_add_post_control() {
    $post = $GLOBALS['post'];
    $DW = &$GLOBALS['DW'];

    $opt = $DW->getOptions('%','individual');
    echo '<strong>' . __('Apply exception rule to widgets:', DW_L10N_DOMAIN) . '</strong><br /><br />';
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

  /**
   * dynwid_add_tag_page() Add row to WP tags admin
   * @since 1.2
   */
  function dynwid_add_tag_page() {
    $DW = &$GLOBALS['DW'];

    // Only show dynwid row when there are widgets enabled
    $opt = $DW->getOptions('%','individual');
    if ( count($opt) > 0 ) {

      echo '<tr class="form-field">';
      echo '<th scope="row" valign="top"><label for="dynamic-widgets">' . __('Dynamic Widgets', DW_L10N_DOMAIN) . '</label></th>';
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

  /**
   * dynwid_add_widget_control() Preparation for callback hook into WP widgets admin
   * @since 1.2
   */
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
          Warning: Cannot use a scalar value as an array in ./wp-content/plugins/dynamic-widgets/dynamic-widgets.php
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
    if ( isset($_GET['dynwid_save']) && $_GET['dynwid_save'] == 'yes' ) {
      add_action('sidebar_admin_page', 'dynwid_add_widget_page');
    }
  }

  /**
   * dynwid_add_widget_page() Save success message for WP widgets admin
	 * @since 1.2
   */
  function dynwid_add_widget_page() {
    $DW = &$GLOBALS['DW'];

    $name = strip_tags($DW->getName($_GET['widget_id']));

    echo '<div class="updated fade" id="message">';
    echo '<p>';
    echo '<strong>' . __('Dynamic Widgets Options saved', DW_L10N_DOMAIN) . '</strong> ' . __('for', DW_L10N_DOMAIN) . ' ' .  $name;
    echo '</p>';
    echo '</div>';
  }

  /**
   * dynwid_admin_dump() Dump function
   * @since 1.0
   */
  function dynwid_admin_dump() {
    $DW = &$GLOBALS['DW'];

    header('Content-Description: File Transfer');
    header('Content-Disposition: attachment; filename=dynwid_dump_' . date('Ymd') . '.txt' );
    header('Content-Type: text/plain');

    $DW->dump();
    die();
  }

  /**
   * dynwid_admin_page() Admin pages
   * @since 1.0
   */
  function dynwid_admin_page() {
    $DW = &$GLOBALS['DW'];
    require_once(dirname(__FILE__) . '/dynwid_admin.php');
  }

  /**
   * dynwid_check_version() Displays changelog with latest version compared to installed version
   * @param mixed $plugin_data
   * @param object $r
   * @since 1.3.1
   */
  function dynwid_check_version($plugin_data, $r) {
    $check = wp_remote_fopen(DW_VERSION_URL_CHECK . $r->new_version);

    if ( $check && ! empty($check) ) {
      echo '<div style="font-weight:normal;">';
      echo $check;
      echo '</div>';
    }
  }

  /**
   * dynwid_filter_init() Init of the worker
   * @since 1.3.5
   */
  function dynwid_filter_init() {
  	$DW = &$GLOBALS['DW'];
  	require(dirname(__FILE__) . '/dynwid_init_worker.php');
  }

  /**
   * dynwid_filter_widgets() Worker
   * @since 1.3.5
   */
  function dynwid_filter_widgets() {
  	$DW = &$GLOBALS['DW'];

  	dynwid_filter_init();
  	if ( DW_OLD_METHOD ) {
  		dynwid_worker($DW->sidebars);
  	} else {
  		add_filter('sidebars_widgets', 'dynwid_worker');
 		}
  }

  /**
   * dynwid_init() Init of the plugin
   * @since 1.0
   */
  function dynwid_init() {
    $GLOBALS['DW'] = new dynWid();
  	$DW = &$GLOBALS['DW'];

  	if ( is_admin() ) {
  	  if ( isset($_POST['dynwid_save']) && $_POST['dynwid_save'] == 'yes' ) {
  	    require_once(dirname(__FILE__) . '/dynwid_admin_save.php');
  	  }

  		load_plugin_textdomain(DW_L10N_DOMAIN, FALSE, dirname(plugin_basename(__FILE__)) . '/locale');

			add_action('admin_menu', 'dynwid_add_admin_menu');
  		if ( $DW->enabled ) {
  			add_action('edit_tag_form_fields', 'dynwid_add_tag_page');
  			add_action('edited_term', 'dynwid_save_tagdata');
  			add_action('in_plugin_update_message-' . plugin_basename(__FILE__), 'dynwid_check_version', 10, 2);
  			add_action('plugin_action_links_' . plugin_basename(__FILE__), 'dynwid_add_plugin_actions');
  			add_action('save_post', 'dynwid_save_postdata');
  			add_action('sidebar_admin_setup', 'dynwid_add_widget_control');
  		}
		} else {
			if ( $DW->enabled ) {
				add_action('wp_head', 'dynwid_filter_widgets');
			}
		}
  }

	/**
	 * dynwid_install() Installation
	 * @since 1.3.1
	 */
	function dynwid_install() {
		if ( function_exists('is_multisite') ) {
			if ( is_multisite() && isset($_GET['networkwide']) && $_GET['networkwide'] == '1' ) {
				$plugin = plugin_basename(__FILE__);
				deactivate_plugins($plugin);
			} else {
				dynwid_activate();
			}
		} else {
			dynwid_activate();
		}
	}

	/**
	 * dynwid_save_postdata() Save of options via post screen
	 * @param int $post_id
	 * @since 1.2
	 */
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

	/**
	 * dynwid_save_tagdata() Save of tagdata
	 * @param int $term_id
	 * @since 1.2
	 */
	function dynwid_save_tagdata($term_id) {
	  // Only act when tag is updated via 'edit', NOT via 'quick edit'
	  if ( $_POST['action'] == 'editedtag' ) {
	    $DW = &$GLOBALS['DW'];

	    if ( array_key_exists('tag_ID', $_POST) ) {
	      $term_id = $_POST['tag_ID'];
	    }

	    // Housekeeping
	    $opt = $DW->getOptions('%', 'individual');
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

	/**
	 * dynwid_sql_mode() Internal check for STRICT sql mode
	 * @since 1.3.6
	 */
	function dynwid_sql_mode() {
		$wpdb = $GLOBALS['wpdb'];
		$strict_mode = array('STRICT_TRANS_TABLES', 'STRICT_ALL_TABLES');

		$query = "SELECT @@GLOBAL.sql_mode";
		$result = $wpdb->get_var($query);
		$sql_global = explode(',', $result);

		$query = "SELECT @@SESSION.sql_mode";
		$result =  $wpdb->get_var($query);
		$sql_session = explode(',', $result);

		$sqlmode = array_merge($sql_global, $sql_session);
		if ( (bool) array_intersect($sql_session, $strict_mode) ) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/**
	 * dynwid_uninstall() Uninstall
	 * @since 1.0
	 */
	function dynwid_uninstall() {
		$wpdb = $GLOBALS['wpdb'];
	  $dbtable = $wpdb->prefix . DW_DB_TABLE;

    // Housekeeping
		delete_option('dynwid_housekeeping_lastrun');
		delete_option('dynwid_old_method');
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

	/**
	 * dynwid_widget_callback() Callback function for hooking into WP widgets admin
	 * @since 1.2
	 */
	function dynwid_widget_callback() {
	  $DW = &$GLOBALS['DW'];

	  $args = func_get_args();
	  $widget_id = $args[0]['widget_id'];
	  $wp_callback = $DW->registered_widget_controls[$widget_id]['wp_callback'];

	  // Calling original callback first
    call_user_func_array($wp_callback, $args);

	  // Now adding the dynwid text & link
	  echo '<p>Dynamic Widgets: ';
		echo '<a style="text-decoration:none;" title="Edit Dynamic Widgets Options" href="themes.php?page=dynwid-config&action=edit&id=' . $widget_id . '&returnurl=' . urlencode(trailingslashit(admin_url()) . 'widgets.php') . '">';
		echo ( $DW->hasOptions($widget_id) ) ? __('Dynamic', DW_L10N_DOMAIN) : __('Static', DW_L10N_DOMAIN);
		echo '</a>';
	  if ( $DW->hasOptions($widget_id) ) {
	    $s = array();
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
	      if (! empty($DW->dwoptions[$type]) ) {
	        $string .= $DW->dwoptions[$type];
	      }
	      $string .= ( ($last - 1) == $i ) ? ' ' . __('and', DW_L10N_DOMAIN) . ' ' : ', ';
	    }
	    $type = $s[$last];
	    $string .= $DW->dwoptions[$type];

	    $output  = '<br /><small>';
	    $output .= ( count($opt) > 1 ) ? __('Options set for', DW_L10N_DOMAIN) : __('Option set for', DW_L10N_DOMAIN);
      $output .= ' ' . $string . '.</small>';
	    echo $output;
	  }
	  echo '</p>';
	}

	/**
	 * dynwid_worker() Worker process
	 *
	 * @param array $sidebars
	 * @return array
	 * @since 1.0
	 */
	function dynwid_worker($sidebars) {
	  $DW = &$GLOBALS['DW'];

		if ( $DW->listmade ) {
			$DW->message('Dynamic Widgets removelist already created');
			if ( count($DW->removelist) > 0 ) {
				foreach ( $DW->removelist as $sidebar_id => $widgets ){
					foreach ( $widgets as $widget_key ){
						unset($sidebars[$sidebar_id][$widget_key]);
					}
				}
			}
		} else {
			if ( $DW->wpsc ) {
				$wpsc_query = &$GLOBALS['wpsc_query'];
			}
			require(dirname(__FILE__) . '/dynwid_worker.php');
		}

		return $sidebars;
	}

  // Hooks
  add_action('admin_action_dynwid_dump', 'dynwid_admin_dump');
  add_action('admin_action_dynwid_uninstall', 'dynwid_uninstall');
  add_action('init', 'dynwid_init');
  register_activation_hook(__FILE__, 'dynwid_install');
?>