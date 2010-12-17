<?php
/**
 * dynwid_admin_edit.php - Options settings
 *
 * @version $Id$
 */

	// WPML Plugin support
	if ( defined('ICL_PLUGIN_PATH') && file_exists(ICL_PLUGIN_PATH . DW_WPML_API) ) {
		$DW->wpml = TRUE;
		$wpml_icon = '<img src="' . $DW->plugin_url . DW_WPML_ICON . '" alt="WMPL" title="Dynamic Widgets syncs with other languages of these pages via WPML" />';
		$wpml_api = ICL_PLUGIN_PATH . DW_WPML_API;
		require_once($wpml_api);
	}

	// WPSC/WPEC Plugin support (http://getshopped.org)
	if ( defined('WPSC_TABLE_PRODUCT_CATEGORIES') ) {
		$DW->wpsc = TRUE;
		require_once(DW_PLUGIN . 'wpsc.php');
	}

  // Roles
  $wp_roles = $GLOBALS['wp_roles'];
  $roles = array_merge($wp_roles->role_names, array('anonymous' => __('Anonymous') . '|User role'));
  $jsroles = array();
  foreach ( $roles as $rid => $role ) {
    $roles[esc_attr($rid)] = translate_user_role($role);
    $jsroles[ ] = '\'role_act_' . esc_attr($rid) . '\'';    // Prep for JS Array
  }
  if ( count($roles) > DW_LIST_LIMIT ) {
    $role_condition_select_style = DW_LIST_STYLE;
  }

  $role_yes_selected = 'checked="checked"';
  $opt_role = $DW->getOptions($_GET['id'], 'role');
  if ( count($opt_role) > 0 ) {
    $role_act = array();
    foreach ( $opt_role as $role_condition ) {
      if ( $role_condition['name'] == 'default' || empty($role_condition['name']) ) {
        $role_default = $role_condition['value'];
      } else {
        $role_act[ ] = $role_condition['name'];
      }
    }

    if ( $role_default == '0' ) {
      $role_no_selected = $role_yes_selected;
      unset($role_yes_selected);
    }
  }

  // Date
  $date_yes_selected = 'checked="checked"';
  $opt_date = $DW->getOptions($_GET['id'], 'date');
  if ( count($opt_date) > 0 ) {
    foreach ( $opt_date as $value ) {
      switch ( $value['name'] ) {
        case 'date_start':
          $date_start = $value['value'];
        break;

        case 'date_end':
          $date_end = $value['value'];
        break;
      }
    }

    $date_no_selected = $date_yes_selected;
    unset($date_yes_selected);
  }

  // Front Page
  if ( get_option('show_on_front') != 'page' ) {
    $frontpage_yes_selected = 'checked="checked"';
    $opt_frontpage = $DW->getOptions($_GET['id'], 'front-page');
    if ( count($opt_frontpage) > 0 ) {
      $frontpage_condition = $opt_frontpage[0]['value'];
      if ( $frontpage_condition == '0' ) {
        $frontpage_no_selected = $frontpage_yes_selected;
        unset($frontpage_yes_selected);
      }
    }
  }

  // Single Post
  $single_yes_selected = 'checked="checked"';
  $single_condition = '1';
  $opt_single = $DW->getOptions($_GET['id'], 'single');
  if ( count($opt_single) > 0 ) {
    foreach ( $opt_single as $widget ) {
      if ( $widget['maintype'] == 'single' ) {
        $single_condition = $widget['value'];
      }
    }
    if ( $single_condition == '0' ) {
      $single_no_selected = $single_yes_selected;
      unset($single_yes_selected);
    }
  }

  // -- Author
  $js_count = 0;
  $opt_single_author = $DW->getOptions($_GET['id'], 'single-author');
  $js_author_array = array();
  if ( count($opt_single_author) > 0 ) {
    $js_count = $js_count + count($opt_single_author) - 1;
    $single_author_act = array();
    foreach ( $opt_single_author as $single_author_condition ) {
      $single_author_act[ ] = $single_author_condition['name'];
    }
  }

  // -- Category
  $opt_single_category = $DW->getOptions($_GET['id'], 'single-category');
  $js_category_array = array();
  if ( count($opt_single_category) > 0 ) {
    $js_count = $js_count + count($opt_single_category) - 1;
    $single_category_act = array();
    foreach ( $opt_single_category as $single_category_condition ) {
      $single_category_act[ ] = $single_category_condition['name'];
    }
  }

  // -- Individual / Posts / Tags
  $individual = FALSE;
  $opt_individual = $DW->getOptions($_GET['id'], 'individual');
  $single_post_act = array();
  $single_tag_act = array();

  if ( count($opt_individual) > 0 ) {
    $individual_condition = $opt_individual[0]['value'];
    if ( $individual_condition == 1 ) {
      $individual = TRUE;

      $opt_single_post = $DW->getOptions($_GET['id'], 'single-post');
      if ( count($opt_single_post) > 0 ) {
        foreach ( $opt_single_post as $single_post_condition ) {
          if ( $single_post_condition['name'] != 'default' ) {
            $single_post_act[ ] = $single_post_condition['name'];
          }
        }
      }

      $opt_single_tag = $DW->getOptions($_GET['id'], 'single-tag');
      if ( count($opt_single_tag) > 0 ) {
        foreach ( $opt_single_tag as $single_tag_condition ) {
          if ( $single_tag_condition['name'] != 'default' ) {
            $single_tag_act[ ] = $single_tag_condition['name'];
          }
        }
      }

      $count_individual = '(Posts: ' . count($single_post_act) . ', Tags: ' . count($single_tag_act) . ')';
    }
  }

  // Pages
  $page_yes_selected = 'checked="checked"';
  $opt_page = $DW->getOptions($_GET['id'], 'page');
  if ( count($opt_page) > 0 ) {
    $page_act = array();
    foreach ( $opt_page as $page_condition ) {
    	if ( $page_condition['maintype'] == 'page' ) {
    		if ( $page_condition['name'] == 'default' || empty($page_condition['name']) ) {
    			$page_default = $page_condition['value'];
    		} else {
    			$page_act[ ] = $page_condition['name'];
    		}
    	}
    }

    if ( $page_default == '0' ) {
      $page_no_selected = $page_yes_selected;
      unset($page_yes_selected);
    }

  	// -- Childs
  	$opt_page_childs = $DW->getOptions($_GET['id'], 'page-childs');
  	if ( count($opt_page_childs) > 0 ) {
  		$page_childs_act = array();
  		foreach ( $opt_page_childs as $child_condition ) {
  			if ( $child_condition['name'] != 'default' ) {
  				$page_childs_act[ ] = $child_condition['name'];
  			}
  		}
  	}
  }

  $pages = get_pages();
  if ( count($pages) > DW_LIST_LIMIT ) {
    $page_condition_select_style = DW_LIST_STYLE;
  }

	$static_page = array();
  if ( get_option('show_on_front') == 'page' ) {
    if ( get_option('page_on_front') == get_option('page_for_posts') ) {
      $id = get_option('page_on_front');
      $static_page[$id] = 'Front page, Posts page';
    } else {
      $id = get_option('page_on_front');
      $static_page[$id] = 'Front page';
      $id = get_option('page_for_posts');
      $static_page[$id] = 'Posts page';
    }
  }

  // Author
  $author_yes_selected = 'checked="checked"';
  $opt_author = $DW->getOptions($_GET['id'], 'author');
  if ( count($opt_author) > 0 ) {
    $author_act = array();
    foreach ( $opt_author as $author_condition ) {
      if ( $author_condition['name'] == 'default' || empty($author_condition['name']) ) {
        $author_default = $author_condition['value'];
      } else {
        $author_act[ ] = $author_condition['name'];
      }
    }

    if ( $author_default == '0' ) {
      $author_no_selected = $author_yes_selected;
      unset($author_yes_selected);
    }
  }

  $authors = get_users_of_blog();
  if ( count($authors) > DW_LIST_LIMIT ) {
    $author_condition_select_style = DW_LIST_STYLE;
  }

  // Categories
  $category_yes_selected = 'checked="checked"';
  $opt_category = $DW->getOptions($_GET['id'], 'category');
  if ( count($opt_category) > 0 ) {
    $category_act = array();
    foreach ( $opt_category as $category_condition ) {
      if ( $category_condition['name'] == 'default' || empty($category_condition['name']) ) {
        $category_default = $category_condition['value'];
      } else {
        $category_act[ ] = $category_condition['name'];
      }
    }

    if ( $category_default == '0' ) {
      $category_no_selected = $category_yes_selected;
      unset($category_yes_selected);
    }
  }

  $category = get_categories( array('hide_empty' => FALSE) );
  if ( count($category) > DW_LIST_LIMIT ) {
    $category_condition_select_style = DW_LIST_STYLE;
  }

  // Archives
  $archive_yes_selected = 'checked="checked"';
  $opt_archive = $DW->getOptions($_GET['id'], 'archive');
  if ( count($opt_archive) > 0 ) {
    $archive_condition = $opt_archive[0]['value'];
    if ( $archive_condition == '0' ) {
      $archive_no_selected = $archive_yes_selected;
      unset($archive_yes_selected);
    }
  }

  // Error 404
  $e404_yes_selected = 'checked="checked"';
  $opt_e404 = $DW->getOptions($_GET['id'], 'e404');
  if ( count($opt_e404) > 0 ) {
  	$e404_condition = $opt_e404[0]['value'];
  	if ( $e404_condition == '0' ) {
  		$e404_no_selected = $e404_yes_selected;
  		unset($e404_yes_selected);
  	}
  }

  // Search
  $search_yes_selected = 'checked="checked"';
  $opt_search = $DW->getOptions($_GET['id'], 'search');
  if ( count($opt_search) > 0 ) {
    $search_condition = $opt_search[0]['value'];
    if ( $search_condition == '0' ) {
      $search_no_selected = $search_yes_selected;
      unset($search_yes_selected);
    }
  }

  // WPML
  if ( $DW->wpml ) {
  	$wpml_yes_selected = 'checked="checked"';
  	$opt_wpml = $DW->getOptions($_GET['id'], 'wpml');
  	if ( count($opt_wpml) > 0 ) {
  		$wpml_act = array();
  		foreach ( $opt_wpml as $wpml_condition ) {
  			if ( $wpml_condition['name'] == 'default' || empty($wpml_condition['name']) ) {
  				$wpml_default = $wpml_condition['value'];
  			} else {
  				$wpml_act[ ] = $wpml_condition['name'];
  			}
  		}

  		if ( $wpml_default == '0' ) {
  			$wpml_no_selected = $wpml_yes_selected;
  			unset($wpml_yes_selected);
  		}
  	}

  	$wpml_langs = wpml_get_active_languages();
  	if ( count($wpml_langs) > DW_LIST_LIMIT ) {
  		$wpml_condition_select_style = DW_LIST_STYLE;
  	}
  }

  // WPSC/WPEC
  if ( $DW->wpsc ) {
  	// Categories
  	$wpsc_yes_selected = 'checked="checked"';
  	$opt_wpsc = $DW->getOptions($_GET['id'], 'wpsc');
  	if ( count($opt_wpsc) > 0 ) {
    	$wpsc_act = array();
    	foreach ( $opt_wpsc as $wpsc_condition ) {
      	if ( $wpsc_condition['name'] == 'default' || empty($wpsc_condition['name']) ) {
        	$wpsc_default = $wpsc_condition['value'];
      	} else {
        	$wpsc_act[ ] = $wpsc_condition['name'];
      	}
    	}

    	if ( $wpsc_default == '0' ) {
      	$wpsc_no_selected = $wpsc_yes_selected;
      	unset($wpsc_yes_selected);
    	}
  	}

  	$wpsc = dw_wpsc_get_categories();
  	if ( count($wpsc) > DW_LIST_LIMIT ) {
    	$wpsc_condition_select_style = DW_LIST_STYLE;
  	}
  }
?>

<style type="text/css">
label {
  cursor : default;
}

.condition-select {
  width : 300px;
  -moz-border-radius-topleft : 6px;
  -moz-border-radius-topright : 6px;
  -moz-border-radius-bottomleft : 6px;
  -moz-border-radius-bottomright : 6px;
  border-style : solid;
  border-width : 1px;
  border-color : #E3E3E3;
  padding : 5px;
}

.infotext {
  width : 98%;
  display : none;
  color : #666666;
  font-style : italic;
}

h4 {
	text-indent : 30px;
}

.hasoptions {
	color : #ff0000;
}

#dynwid {
	font-family : 'Lucida Grande', Verdana, Arial, 'Bitstream Vera Sans', sans-serif;
	font-size : 13px;
}

.ui-datepicker {
	font-size : 10px;
}
</style>

<?php if ( isset($_POST['dynwid_save']) && $_POST['dynwid_save'] == 'yes' ) { ?>
<div class="updated fade" id="message">
  <p>
    <strong><?php _e('Widget options saved.', DW_L10N_DOMAIN); ?></strong> <a href="themes.php?page=dynwid-config"><?php _e('Return', DW_L10N_DOMAIN); ?></a> <?php _e('to Dynamic Widgets overview', DW_L10N_DOMAIN); ?>.
  </p>
</div>
<?php } else if ( isset($_GET['work']) && $_GET['work'] == 'none' ) { ?>
<div class="error" id="message">
  <p><?php echo __('Dynamic does not mean static hiding of a widget.', DW_L10N_DOMAIN) . ' ' . __('Hint', DW_L10N_DOMAIN) . ': '; ?><a href="widgets.php"><?php _e('Remove', DW_L10N_DOMAIN); ?></a> <?php _e('the widget from the sidebar', DW_L10N_DOMAIN); ?>.</p>
</div>
<?php } else if ( isset($_GET['work']) && $_GET['work'] == 'nonedate' ) { ?>
<div class="error" id="message">
  <p><?php _e('The From date can\'t be later than the To date.', DW_L10N_DOMAIN); ?></p>
</div>

<?php } ?>

<h3><?php _e('Edit options for', DW_L10N_DOMAIN); ?> <em><?php echo $DW->getName($_GET['id']); ?></em> <?php _e('Widget'); ?></h3>
<?php echo ( DW_DEBUG ) ? '<pre>ID = ' . $_GET['id'] . '</pre><br />' : ''; ?>

<form action="<?php echo trailingslashit(admin_url()) . 'themes.php?page=dynwid-config&action=edit&id=' . $_GET['id']; ?>" method="post">
<?php wp_nonce_field('plugin-name-action_edit_' . $_GET['id']); ?>
<input type="hidden" name="dynwid_save" value="yes" />
<input type="hidden" name="widget_id" value="<?php echo $_GET['id']; ?>" />
<input type="hidden" name="returnurl" value="<?php echo ( isset($_GET['returnurl']) ? urldecode($_GET['returnurl']) : '' ); ?>" />

<div id="dynwid">
<h4><b><?php _e('Role'); ?></b><?php echo ( count($opt_role) > 0 ? ' <span class="hasoptions">*</span>' : '' ); ?></h4>
<div class="dynwid_conf">
<?php _e('Show widget to everybody?', DW_L10N_DOMAIN); ?> <img src="<?php echo $DW->plugin_url; ?>img/info.gif" alt="info" onclick="divToggle('role');" /><br />
<?php $DW->dumpOpt($opt_role); ?>
<div>
	<div id="role" class="infotext">
  <?php _e('Setting options by role is very powerfull. It can override all other options!<br />
						Users who are not logged in, get the <em>Anonymous</em> role.', DW_L10N_DOMAIN); ?>
	</div>
</div>
<input type="radio" name="role" value="yes" id="role-yes" <?php echo ( isset($role_yes_selected) ? $role_yes_selected : '' ); ?> onclick="swChb(cRole, true);" /> <label for="role-yes"><?php _e('Yes'); ?></label>
<input type="radio" name="role" value="no" id="role-no" <?php echo ( isset($role_no_selected) ? $role_no_selected : '' ); ?> onclick="swChb(cRole, false)" /> <label for="role-no"><?php _e('No'); ?>, <?php _e('only to', DW_L10N_DOMAIN); ?>:</label><br />
<div id="role-select" class="condition-select" <?php echo ( isset($role_condition_select_style) ? $role_condition_select_style : '' ); ?>>
<?php foreach ( $roles as $rid => $role ) { ?>
<input type="checkbox" id="role_act_<?php echo $rid; ?>" name="role_act[]" value="<?php echo $rid; ?>" <?php echo ( isset($role_act) && count($role_act) > 0 && in_array($rid, $role_act) ) ? 'checked="checked"' : ''; ?> /> <label for="role_act_<?php echo $rid; ?>"><?php echo $role; ?></label><br />
<?php } ?>
</div>
</div><!-- end dynwid_conf -->

<h4><b><?php _e('Date'); ?></b><?php echo ( count($opt_date) > 0 ? ' <span class="hasoptions">*</span>' : '' ); ?></h4>
<div class="dynwid_conf">
<?php _e('Show widget always?', DW_L10N_DOMAIN); ?> <img src="<?php echo $DW->plugin_url; ?>img/info.gif" alt="info" onclick="divToggle('date');" /><br />
<?php $DW->dumpOpt($opt_date); ?>
<div>
	<div id="date" class="infotext">
  <?php _e('Next to the above role option, the date option is also very powerfull. You\'ve been warned!', DW_L10N_DOMAIN); ?><br />
  <?php _e('Enter dates in the YYYY-MM-DD format. You can also use the calender by clicking on the', DW_L10N_DOMAIN); ?> <img src="<?php echo $DW->plugin_url; ?>img/calendar.gif" alt="Calendar" /><br />
  <?php _e('Date ranges can be made by entering a From AND a To date<br />
  					When you want the widget to be displayed from a specific date, only fill in the From date<br />
  					When you want the widget to stop displaying on a specific date, only fill in the To date.
  				', DW_L10N_DOMAIN); ?>
	</div>
</div>
<input type="radio" name="date" value="yes" id="date-yes" <?php echo ( isset($date_yes_selected) ? $date_yes_selected : '' ); ?> onclick="swTxt(cDate, true);" /> <label for="date-yes"><?php _e('Yes'); ?></label>
<input type="radio" name="date" value="no" id="date-no" <?php echo ( isset($date_no_selected) ? $date_no_selected : '' ); ?> onclick="swTxt(cDate, false)" /> <label for="date-no"><?php _e('No'); ?>, <?php _e('only', DW_L10N_DOMAIN); ?>:</label><br />
<div id="date-select" class="condition-select">
<table border="0" cellspacing="0" cellpadding="0">
<tr>
  <td style="width:45px;"><?php _e('From', DW_L10N_DOMAIN); ?></td>
  <td><input id="date_start" type="text" name="date_start" value="<?php echo ( isset($date_start) ? $date_start : '' ); ?>" size="10" maxlength="10" /> <img src="<?php echo $DW->plugin_url; ?>img/calendar.gif" alt="Calendar" onclick="showCalendar('date_start')" /></td>
</tr>
<tr>
  <td style="width:45px;"><?php _e('To', DW_L10N_DOMAIN); ?></td>
  <td><input id="date_end" type="text" name="date_end" value="<?php echo ( isset($date_end) ? $date_end : '' ); ?>" size="10" maxlength="10" /> <img src="<?php echo $DW->plugin_url; ?>img/calendar.gif" alt="Calendar" onclick="showCalendar('date_end')" /></td>
</tr>
</table>
</div>
</div><!-- end dynwid_conf -->

<?php if ( $DW->wpml ) { /* WPML */ ?>
<h4><b><?php _e('Language (WPML)', DW_L10N_DOMAIN); ?></b><?php echo ( count($opt_wpml) > 0 ? ' <span class="hasoptions">*</span>' : '' ); ?></h4>
<div class="dynwid_conf">
<?php _e('Show widget default on all languages?', DW_L10N_DOMAIN); ?> <img src="<?php echo $DW->plugin_url; ?>img/info.gif" alt="info" onclick="divToggle('wpml');" /><br /><br />
<?php $DW->dumpOpt($opt_wpml); ?>
<div>
	<div id="wpml" class="infotext">
	<?php _e('Using this option can override all other options.'); ?><br />
	</div>
</div>
<input type="radio" name="wpml" value="yes" id="wpml-yes" <?php echo ( isset($wpml_yes_selected) ? $wpml_yes_selected : '' ); ?> /> <label for="wpml-yes"><?php _e('Yes'); ?></label>
<input type="radio" name="wpml" value="no" id="wpml-no" <?php echo ( isset($wpml_no_selected) ? $wpml_no_selected : '' ); ?> /> <label for="wpml-no"><?php _e('No'); ?></label><br />
<?php _e('Except the languages', DW_L10N_DOMAIN); ?>:<br />
<div id="wpml-select" class="condition-select" <?php echo ( isset($wpml_condition_select_style) ? $wpml_condition_select_style : '' ); ?>>
<?php foreach ( $wpml_langs as $code => $lang ) { ?>
	<input type="checkbox" id="wpml_act_<?php echo $lang['code']; ?>" name="wpml_act[]" value="<?php echo $lang['code']; ?>" <?php echo ( count($wpml_act) > 0 && in_array($lang['code'], $wpml_act) ) ? 'checked="checked"' : ''; ?> /> <label for="wpml_act_<?php echo $lang['code']; ?>"><?php echo $lang['display_name']; ?></label><br />
<?php } ?>
</div>
</div><!-- end dynwid_conf -->
<?php } ?>

<?php if ( get_option('show_on_front') != 'page' ) { ?>
<h4><b><?php _e('Front Page', DW_L10N_DOMAIN); ?></b><?php echo ( count($opt_frontpage) > 0 ? ' <span class="hasoptions">*</span>' : '' ); ?></h4>
<div class="dynwid_conf">
<?php _e('Show widget on the front page?', DW_L10N_DOMAIN) ?> <img src="<?php echo $DW->plugin_url; ?>img/info.gif" alt="info" onclick="divToggle('frontpage');" /><br />
<?php $DW->dumpOpt($opt_frontpage); ?>
<div>
	<div id="frontpage"  class="infotext">
	<?php _e('This option only applies when your front page is set to display your latest posts (See Settings &gt; Reading).<br />
						When a static page is set, you can use the options for the static pages below.
					', DW_L10N_DOMAIN); ?>
	</div>
</div>
<input type="radio" name="front-page" value="yes" id="front-page-yes" <?php echo ( isset($frontpage_yes_selected) ? $frontpage_yes_selected : '' ); ?> /> <label for="front-page-yes"><?php _e('Yes'); ?></label>
<input type="radio" name="front-page" value="no" id="front-page-no" <?php echo ( isset($frontpage_no_selected) ? $frontpage_no_selected : '' ); ?> /> <label for="front-page-no"><?php _e('No'); ?></label>
</div><!-- end dynwid_conf -->
<?php } ?>

<h4><b><?php _e('Single Posts', DW_L10N_DOMAIN); ?></b><?php echo ( count($opt_single) > 0 || count($opt_single_author) > 0 || count($opt_single_category) > 0 || count($opt_single_post) > 0 || count($opt_single_tag) > 0 ? ' <span class="hasoptions">*</span>' : '' ); ?></h4>
<div class="dynwid_conf">
<?php _e('Show widget default on single posts?', DW_L10N_DOMAIN) ?> <img src="<?php echo $DW->plugin_url; ?>img/info.gif" alt="info" title="Click to toggle info" onclick="divToggle('single')" /><br />
<?php $DW->dumpOpt($opt_single); ?>
<div>
	<div id="single" class="infotext">
  <?php _e('When you use an author <b>AND</b> a category exception, both rules in the condition must be met. Otherwise the exception rule won\'t be applied.
  					If you want to use the rules in a logical OR condition. Add the same widget again and apply the other rule to that.
  					', DW_L10N_DOMAIN); ?>
	</div>
</div>
<input type="radio" name="single" value="yes" id="single-yes" <?php echo ( isset($single_yes_selected) ? $single_yes_selected : '' ); ?> /> <label for="single-yes"><?php _e('Yes'); ?></label>
<input type="radio" name="single" value="no" id="single-no" <?php echo ( isset($single_no_selected) ? $single_no_selected : '' ); ?> /> <label for="single-no"><?php _e('No'); ?></label><br />
<?php $DW->dumpOpt($opt_individual); ?>
<input type="checkbox" id="individual" name="individual" value="1" <?php echo ( $individual ) ? 'checked="checked"' : ''; ?> onclick="chkInPosts()" />
<label for="individual"><?php _e('Make exception rule available to individual posts and tags.', DW_L10N_DOMAIN) ?> <?php echo ( isset($count_individual) ? $count_individual : '' ); ?></label>
<img src="<?php echo $DW->plugin_url; ?>img/info.gif" alt="info" title="Click to toggle info" onclick="divToggle('individual_post_tag')" />
<div>
	<div id="individual_post_tag" class="infotext">
  <?php _e('When you enable this option, you have the ability to apply the exception rule for <em>Single Posts</em> to tags and individual posts.
						You can set the exception rule for tags in the single Edit Tag Panel (go to <a href="edit-tags.php?taxonomy=post_tag">Post Tags</a>,
						click a tag), For individual posts in the <a href="post-new.php">New</a> or <a href="edit.php">Edit</a> Posts panel.
						Exception rules for tags and individual posts in any combination work independantly, but will always be counted as one exception.<br />
  					Please note when exception rules are set for Author and/or Category, these will be removed.
  				', DW_L10N_DOMAIN); ?>
	</div>
</div>
<?php foreach ( $single_post_act as $singlepost ) { ?>
<input type="hidden" name="single_post_act[]" value="<?php echo $singlepost; ?>" />
<?php } ?>
<?php foreach ( $single_tag_act as $tag ) { ?>
<input type="hidden" name="single_tag_act[]" value="<?php echo $tag; ?>" />
<?php } ?>
<table border="0" cellspacing="0" cellpadding="0">
<tr>
  <td valign="top">
    <?php _e('Except the posts by author', DW_L10N_DOMAIN); ?>:
    <?php $DW->dumpOpt($opt_single_author); ?>
    <div id="single-author-select" class="condition-select" <?php echo ( isset($author_condition_select_style) ? $author_condition_select_style : '' ); ?>>
    <?php foreach ( $authors as $author ) { ?>
    <?php $js_author_array[ ] = '\'single_author_act_' . $author->ID . '\''; ?>
    <input type="checkbox" id="single_author_act_<?php echo $author->ID; ?>" name="single_author_act[]" value="<?php echo $author->ID; ?>" <?php echo ( isset($single_author_act) && count($single_author_act) > 0 && in_array($author->ID,$single_author_act) ) ? 'checked="checked"' : '';  ?> onclick="ci('single_author_act_<?php echo $author->ID; ?>')" /> <label for="single_author_act_<?php echo $author->ID; ?>"><?php echo $author->display_name; ?></label><br />
    <?php } ?>
    </div>
  </td>
  <td style="width:10px"></td>
  <td valign="top">
    <?php _e('Except the posts in category', DW_L10N_DOMAIN); ?>: <?php echo ( $DW->wpml ? $wpml_icon : '' ); ?>
    <?php $DW->dumpOpt($opt_single_category); ?>
    <div id="single-category-select" class="condition-select" <?php echo ( isset($category_condition_select_style) ? $category_condition_select_style : '' ); ?>>
    <?php foreach ( $category as $cat ) { ?>
    <?php $js_category_array[ ] = '\'single_cat_act_' . $cat->cat_ID . '\''; ?>
    <input type="checkbox" id="single_cat_act_<?php echo $cat->cat_ID; ?>" name="single_category_act[]" value="<?php echo $cat->cat_ID; ?>" <?php echo ( isset($single_category_act) && count($single_category_act) > 0 && in_array($cat->cat_ID,$single_category_act) ) ? 'checked="checked"' : ''; ?> onclick="ci('single_cat_act_<?php echo $cat->cat_ID; ?>')" /> <label for="single_cat_act_<?php echo $cat->cat_ID; ?>"><?php echo $cat->name; ?></label><br />
    <?php } ?>
    </div>
  </td>
</tr>
</table>
</div><!-- end dynwid_conf -->

<h4><b><?php _e('Pages'); ?></b> <?php echo ( count($opt_page) > 0 ? ' <span class="hasoptions">*</span>' : '' ) . ( $DW->wpml ? $wpml_icon : '' ); ?></h4>
<div class="dynwid_conf">
<?php _e('Show widget default on static pages?', DW_L10N_DOMAIN); ?> <img src="<?php echo $DW->plugin_url; ?>img/info.gif" alt="info" onclick="divToggle('pages');" /><br />
<?php $DW->dumpOpt($opt_page); ?>
<div>
	<div id="pages" class="infotext">
	<?php
		$childs_infotext = __('Checking the "All childs" option, makes the exception rule apply
				to the parent and all items under it in all levels. Also future items
				under the parent. It\'s not possible to apply an exception rule to
				"All childs" without the parent.', DW_L10N_DOMAIN);
		echo $childs_infotext;
	?>
	</div>
</div>
<input type="radio" name="page" value="yes" id="page-yes" <?php echo ( isset($page_yes_selected) ? $page_yes_selected : '' ); ?> /> <label for="page-yes"><?php _e('Yes'); ?></label>
<input type="radio" name="page" value="no" id="page-no" <?php echo ( isset($page_no_selected) ? $page_no_selected : '' ); ?> /> <label for="page-no"><?php _e('No'); ?></label><br />
<?php _e('Except the page(s)', DW_L10N_DOMAIN); ?>:<br />
<div id="page-select" class="condition-select" <?php echo ( isset($page_condition_select_style) ? $page_condition_select_style : '' ); ?>>
<div style="position:relative;left:-15px">
<?php
	function getPageChilds($arr, $id, $i) {
		$pg = get_pages('child_of=' . $id);
		foreach ($pg as $p ) {
			if (! in_array($p->ID, $i) ) {
				$i[ ] = $p->ID;
				$arr[$p->ID] = array();
				$a = &$arr[$p->ID];
				$a = getPageChilds($a, $p->ID, &$i);
			}
		}
		return $arr;
	}
	$pagemap = getPageChilds(array(), 0, array());

	// Creating childmap
	function childPageMap($arr, $id) {
		$pg = get_pages('child_of=' . $id);
		foreach ($pg as $p ) {
			$i[ ] = $p->ID;
			$arr[$p->ID] = array();
			$a = &$arr[$p->ID];
			$a = childPageMap($a, $p->ID);
		}
		return $arr;
	}
	$childmap = childPageMap(array(), 0);

	function prtPgs($pages, $childmap, $page_act, $page_childs_act) {
		foreach ( $pages as $pid => $childs ) {
			$page = get_page($pid);

			echo '<div style="position:relative;left:15px;">';
			echo '<input type="checkbox" id="page_act_' . $page->ID . '" name="page_act[]" value="' . $page->ID . '" ' . ( isset($page_act) && count($page_act) > 0 && in_array($page->ID, $page_act) ? 'checked="checked"' : '' ) . ' onchange="chkChild(' . $pid . ')" /> <label for="page_act_' . $page->ID . '">' . $page->post_title . ' ' . ( get_option('show_on_front') == 'page' && isset($static_page[$page->ID]) ? '(' . $static_page[$page->ID] . ')' : '' ) . '</label><br />';

			echo '<div style="position:relative;left:15px;">';
			echo '<input type="checkbox" id="child_' . $pid . '" name="page_childs_act[]" value="' . $pid . '" ' . ( isset($page_childs_act) && count($page_childs_act) > 0 && in_array($pid, $page_childs_act) ? 'checked="checked"' : '' ) . ' onchange="chkParent(' . $pid . ')" /> <label for="child_' . $pid . '"><em>' . __('All childs', DW_L10N_DOMAIN) . '</em></label><br />';
			echo '</div>';

			if ( count($childs) > 0 ) {
				prtPgs($childs, $childmap, $page_act, $page_childs_act);
			}
			echo '</div>';
		}
	}
	prtPgs($pagemap, $childmap, $page_act, $page_childs_act);
?>
</div>
</div>

</div><!-- end dynwid_conf -->

<h4><b><?php _e('Author Pages', DW_L10N_DOMAIN); ?></b><?php echo ( count($opt_author) > 0 ? ' <span class="hasoptions">*</span>' : '' ); ?></h4>
<div class="dynwid_conf">
<?php _e('Show widget default on author pages?', DW_L10N_DOMAIN); ?><br />
<?php $DW->dumpOpt($opt_author); ?>
<input type="radio" name="author" value="yes" id="author-yes" <?php echo ( isset($author_yes_selected) ? $author_yes_selected : '' ); ?> /> <label for="author-yes"><?php _e('Yes'); ?></label>
<input type="radio" name="author" value="no" id="author-no" <?php echo ( isset($author_no_selected) ? $author_no_selected : '' ); ?> /> <label for="author-no"><?php _e('No'); ?></label><br />
<?php _e('Except the author(s)', DW_L10N_DOMAIN); ?>:<br />
<div id="author-select" class="condition-select" <?php echo ( isset($author_condition_select_style) ? $author_condition_select_style : '' ); ?>>
<?php foreach ( $authors as $author ) { ?>
<input type="checkbox" id="author_act_<?php echo $author->ID; ?>" name="author_act[]" value="<?php echo $author->ID; ?>" <?php echo ( isset($author_act) && count($author_act) > 0 && in_array($author->ID,$author_act) ) ? 'checked="checked"' : ''; ?> /> <label for="author_act_<?php echo $author->ID; ?>"><?php echo $author->display_name; ?></label><br />
<?php } ?></div>
</div><!-- end dynwid_conf -->

<h4><b><?php _e('Category Pages', DW_L10N_DOMAIN); ?></b> <?php echo ( count($opt_category) > 0 ? ' <span class="hasoptions">*</span>' : '' ) . ( $DW->wpml ? $wpml_icon : '' ); ?></h4>
<div class="dynwid_conf">
<?php _e('Show widget default on category pages?', DW_L10N_DOMAIN); ?><br />
<?php $DW->dumpOpt($opt_category); ?>
<input type="radio" name="category" value="yes" id="category-yes" <?php echo ( isset($category_yes_selected) ? $category_yes_selected : '' ); ?> /> <label for="category-yes"><?php _e('Yes'); ?></label>
<input type="radio" name="category" value="no" id="category-no" <?php echo ( isset($category_no_selected) ? $category_no_selected : '' ); ?> /> <label for="category-no"><?php _e('No'); ?></label><br />
<?php _e('Except the categories', DW_L10N_DOMAIN); ?>:<br />
<div id="category-select" class="condition-select" <?php echo ( isset($category_condition_select_style) ? $category_condition_select_style : '' ); ?>>
<?php foreach ( $category as $cat ) { ?>
<input type="checkbox" id="cat_act_<?php echo $cat->cat_ID; ?>" name="category_act[]" value="<?php echo $cat->cat_ID; ?>" <?php echo ( isset($category_act) && count($category_act) > 0 && in_array($cat->cat_ID,$category_act) ) ? 'checked="checked"' : ''; ?> /> <label for="cat_act_<?php echo $cat->cat_ID; ?>"><?php echo $cat->name; ?></label><br />
<?php } ?>
</div>
</div><!-- end dynwid_conf -->

<h4><b><?php _e('Archive Pages', DW_L10N_DOMAIN); ?></b><?php echo ( count($opt_archive) > 0 ? ' <span class="hasoptions">*</span>' : '' ); ?></h4>
<div class="dynwid_conf">
<?php _e('Show widget on archive pages', DW_L10N_DOMAIN); ?>? <img src="<?php echo $DW->plugin_url; ?>img/info.gif" alt="info" title="Click to toggle info" onclick="divToggle('archive')" /><br />
<?php $DW->dumpOpt($opt_archive); ?>
<div>
<div id="archive" class="infotext">
  <?php _e('This option does not include Author and Category Pages.', DW_L10N_DOMAIN); ?>
</div>
</div>
<input type="radio" name="archive" value="yes" id="archive-yes" <?php echo ( isset($archive_yes_selected) ? $archive_yes_selected : '' ); ?> /> <label for="archive-yes"><?php _e('Yes'); ?></label>
<input type="radio" name="archive" value="no" id="archive-no" <?php echo ( isset($archive_no_selected) ? $archive_no_selected : '' ); ?> /> <label for="archive-no"><?php _e('No'); ?></label>

</div><!-- end dynwid_conf -->

<h4><b><?php _e('Error Page', DW_L10N_DOMAIN); ?></b><?php echo ( count($opt_e404) > 0 ? ' <span class="hasoptions">*</span>' : '' ); ?></h4>
<div class="dynwid_conf">
<?php _e('Show widget on the error page?', DW_L10N_DOMAIN); ?><br />
<?php $DW->dumpOpt($opt_e404); ?>
<input type="radio" name="e404" value="yes" id="e404-yes" <?php echo ( isset($e404_yes_selected) ? $e404_yes_selected : '' ); ?> /> <label for="e404-yes"><?php _e('Yes'); ?></label>
<input type="radio" name="e404" value="no" id="e404-no" <?php echo ( isset($e404_no_selected) ? $e404_no_selected : '' ); ?> /> <label for="e404-no"><?php _e('No'); ?></label>
</div><!-- end dynwid_conf -->

<h4><b><?php _e('Search Page', DW_L10N_DOMAIN); ?></b><?php echo ( count($opt_search) > 0 ? ' <span class="hasoptions">*</span>' : '' ); ?></h4>
<div class="dynwid_conf">
<?php _e('Show widget on the search page?', DW_L10N_DOMAIN); ?><br />
<?php $DW->dumpOpt($opt_search); ?>
<input type="radio" name="search" value="yes" id="search-yes" <?php echo ( isset($search_yes_selected) ? $search_yes_selected : '' ); ?> /> <label for="search-yes"><?php _e('Yes'); ?></label>
<input type="radio" name="search" value="no" id="search-no" <?php echo ( isset($search_no_selected) ? $search_no_selected : '' ); ?> /> <label for="search-no"><?php _e('No'); ?></label>
</div><!-- end dynwid_conf -->

<?php
  /* WordPress 3.0 and higher: Custom Post Types */
  if ( version_compare($GLOBALS['wp_version'], '3.0', '>=') ) {
  	function getCPostChilds($type, $arr, $id, $i) {
  		$post = get_posts('post_type=' . $type . '&post_parent=' . $id);
  		foreach ($post as $p ) {
  			if (! in_array($p->ID, $i) ) {
  				$i[ ] = $p->ID;
  				$arr[$p->ID] = array();
  				$a = &$arr[$p->ID];
  				$a = getCPostChilds($type, $a, $p->ID, &$i);
  			}
  		}
  		return $arr;
  	}

  	function childCPostMap($type, $arr, $id) {
  		$post = get_posts('post_type=' . $type . '&post_parent=' . $id);
  		foreach ($post as $p ) {
  			$i[ ] = $p->ID;
  			$arr[$p->ID] = array();
  			$a = &$arr[$p->ID];
  			$a = childCPostMap($type, $a, $p->ID);
  		}
  		return $arr;
  	}

  	function prtCPost($type, $ctid, $posts, $childmap, $posts_act, $posts_childs_act) {
  		foreach ( $posts as $pid => $childs ) {
  			$post = get_post($pid);

  			echo '<div style="position:relative;left:15px;">';
  			echo '<input type="checkbox" id="' . $type . '_act_' . $post->ID . '" name="' . $type . '_act[]" value="' . $post->ID . '" ' . ( isset($posts_act) && count($posts_act) > 0 && in_array($post->ID, $posts_act) ? 'checked="checked"' : '' ) . ' onchange="chkCPChild(\'' . $type . '\',' . $pid . ')" /> <label for="' . $type . '_act_' . $post->ID . '">' . $post->post_title . '</label><br />';

  			if ( $ctid->hierarchical ) {
  				echo '<div style="position:relative;left:15px;">';
  				echo '<input type="checkbox" id="' . $type . '_child_' . $pid . '" name="' . $type . '_childs_act[]" value="' . $pid . '" ' . ( isset($posts_childs_act) && count($posts_childs_act) > 0 && in_array($pid, $posts_childs_act) ? 'checked="checked"' : '' ) . ' onchange="chkCPParent(\'' . $type . '\',' . $pid . ')" /> <label for="' . $type . '_child_' . $pid . '"><em>' . __('All childs', DW_L10N_DOMAIN) . '</em></label><br />';
  				echo '</div>';
  			}

  			if ( count($childs) > 0 ) {
  				prtCPost($type, $ctid, $childs, $childmap, $posts_act, $posts_childs_act);
  			}
  			echo '</div>';
  		}
  	}

    $args = array(
              'public'   => TRUE,
              '_builtin' => FALSE
            );
    $post_types = get_post_types($args, 'objects', 'and');

    foreach ( $post_types as $type => $ctid ) {
      // Prepare
      $custom_yes_selected = 'checked="checked"';
      $opt_custom = $DW->getOptions($_GET['id'], $type);
      if ( count($opt_custom) > 0 ) {
        $custom_act = array();
      	$custom_childs_act = array();

        foreach ( $opt_custom as $custom_condition ) {
        	if ( $custom_condition['maintype'] == $type ) {
         	 if ( $custom_condition['name'] == 'default' || empty($custom_condition['name']) ) {
          	  $custom_default = $custom_condition['value'];
          	} else {
            	$custom_act[ ] = $custom_condition['name'];
          	}
       	 	}
      	}

        if ( $custom_default == '0' ) {
          $custom_no_selected = $custom_yes_selected;
          unset($custom_yes_selected);
        }

	    	// -- Childs
      	if ( $ctid->hierarchical ) {
      		$opt_custom_childs = $DW->getOptions($_GET['id'], $type . '-childs');
      		if ( count($opt_custom_childs) > 0 ) {
      			foreach ( $opt_custom_childs as $child_condition ) {
      				if ( $child_condition['name'] != 'default' ) {
      					$custom_childs_act[ ] = $child_condition['name'];
      				}
      			}
      		}
      	}
      }

      $loop = new WP_Query( array('post_type' => $type) );
      if ( $loop->post_count > DW_LIST_LIMIT ) {
        $custom_condition_select_style = DW_LIST_STYLE;
      }

    	$cpmap = getCPostChilds($type, array(), 0, array());
    	$childmap = childCPostMap($type, array(), 0);

      // Output
      echo '<h4><b>' . __('Custom Post Type') . ' <em>' . $ctid->label . '</em></b> ' . ( count($opt_custom) > 0 ? ' <span class="hasoptions">*</span>' : '' ) . ( $DW->wpml ? $wpml_icon : '' ) . '</h4>';
      echo '<div class="dynwid_conf">';
      echo __('Show widget on', DW_L10N_DOMAIN) . ' ' . $ctid->label . '? ' . ( $ctid->hierarchical ? '<img src="' . $DW->plugin_url . 'img/info.gif" alt="info" onclick="divToggle(\'custom_' . $type . '\');" />' : '' ) . '<br />';
      echo '<input type="hidden" name="post_types[]" value="' . $type . '" />';
      $DW->dumpOpt($opt_custom);

    	if ( $ctid->hierarchical ) {
    		echo '<div>';
    		echo '<div id="custom_' . $type . '" class="infotext">';
    		echo $childs_infotext;
    		echo '</div>';
    		echo '</div>';
    	}

			echo '<input type="radio" name="' . $type . '" value="yes" id="' . $type . '-yes" ' . ( isset($custom_yes_selected) ? $custom_yes_selected : '' ) . ' /> <label for="' . $type . '-yes">' . __('Yes') . '</label> ';
      echo '<input type="radio" name="' . $type . '" value="no" id="' . $type . '-no" ' . ( isset($custom_no_selected) ? $custom_no_selected : '' ) . ' /> <label for="' . $type . '-no">' . __('No') . '</label><br />';

      echo __('Except for') . ':<br />';
      echo '<div id="' . $type . '-select" class="condition-select" ' . ( isset($custom_condition_select_style) ? $custom_condition_select_style : '' ) . '>';

    	echo '<div style="position:relative;left:-15px">';
    	prtCPost($type, $ctid, $cpmap, $childmap, $custom_act, $custom_childs_act);
    	echo '</div>';

      echo '</div>';
      echo '</div><!-- end dynwid_conf -->';
    }
  } // end version compare >= WP 3.0

	// WPEC
  if ( $DW->wpsc ) {
?>
<h4><b><?php _e('WPSC Category', DW_L10N_DOMAIN); ?></b><?php echo ( count($opt_wpsc) > 0 ? ' <span class="hasoptions">*</span>' : '' ); ?></h4>
<div class="dynwid_conf">
<?php _e('Show widget default on WPSC categories?', DW_L10N_DOMAIN); ?><br />
<?php $DW->dumpOpt($opt_wpsc); ?>
<input type="radio" name="wpsc" value="yes" id="wpsc-yes" <?php echo ( isset($wpsc_yes_selected) ? $wpsc_yes_selected : '' ); ?> /> <label for="wpsc-yes"><?php _e('Yes'); ?></label>
<input type="radio" name="wpsc" value="no" id="wpsc-no" <?php echo ( isset($wpsc_no_selected) ? $wpsc_no_selected : '' ); ?> /> <label for="wpsc-no"><?php _e('No'); ?></label><br />
<?php _e('Except the categories', DW_L10N_DOMAIN); ?>:<br />
<div id="wpsc-select" class="condition-select" <?php echo ( isset($wpsc_condition_select_style) ? $wpsc_condition_select_style : '' ); ?>>
<?php foreach ( $wpsc as $id => $cat ) { ?>
<input type="checkbox" id="wpsc_act_<?php echo $id; ?>" name="wpsc_act[]" value="<?php echo $id; ?>" <?php echo ( count($wpsc_act) > 0 && in_array($id, $wpsc_act) ) ? 'checked="checked"' : ''; ?> /> <label for="wpsc_act_<?php echo $id; ?>"><?php echo $cat; ?></label><br />
<?php } ?>
</div>
</div><!-- end dynwid_conf -->
<?php
  } // DW->wpsc
?>

</div><!-- end dynwid -->

<br />
<div style="float:left">
<input class="button-primary" type="submit" value="<?php _e('Save'); ?>" /> &nbsp;&nbsp;
</div>
<?php $url = (! empty($_GET['returnurl']) ) ? urldecode($_GET['returnurl']) : trailingslashit(admin_url()) . 'themes.php?page=dynwid-config'; ?>
<div style="float:left">
<input class="button-secondary" type="button" value="<?php _e('Return', DW_L10N_DOMAIN); ?>" onclick="location.href='<?php echo $url; ?>'" />
</div>

</form>


<script type="text/javascript">
  function chkInPosts() {
    var posts = <?php echo count($single_post_act); ?>;
    var tags = <?php echo count($single_tag_act); ?>;

    if ( (posts > 0 || tags > 0) && document.getElementById('individual').checked == false ) {
      if ( confirm('Are you sure you want to disable the exception rule for individual posts and tags?\nThis will remove the options set to individual posts and/or tags for this widget.\nOk = Yes; No = Cancel') ) {
        swChb(cAuthors, false);
        swChb(cCat, false);
      } else {
        jQuery('#individual').attr('checked', true);
      }
    } else if ( icount > 0 && document.getElementById('individual').checked ) {
      if ( confirm('Are you sure you want to enable the exception rule for individual posts and tags?\nThis will remove the exceptions set for Author and/or Category on single posts for this widget.\nOk = Yes; No = Cancel') ) {
        swChb(cAuthors, true);
        swChb(cCat, true);
        icount = 0;
      } else {
        jQuery('#individual').attr('checked', false);
      }
    } else if ( jQuery('#individual').attr('checked') ) {
        swChb(cAuthors, true);
        swChb(cCat, true);
    } else {
        swChb(cAuthors, false);
        swChb(cCat, false);
    }
  }

  function chkChild(pid) {
  	if ( jQuery('#page_act_'+pid).attr('checked') == false ) {
  		jQuery('#child_'+pid).attr('checked', false);
  	}
  }

  function chkParent(pid) {
  	if ( jQuery('#child_'+pid).attr('checked') == true ) {
  		jQuery('#page_act_'+pid).attr('checked', true);
  	}
  }

  function chkCPChild(type, pid) {
  	if ( jQuery('#'+type+'_act_'+pid).attr('checked') == false ) {
  		jQuery('#'+type+'_child_'+pid).attr('checked', false);
  	}
  }

  function chkCPParent(type, pid) {
  	if ( jQuery('#'+type+'_child_'+pid).attr('checked') == true ) {
  		jQuery('#'+type+'_act_'+pid).attr('checked', true);
  	}
  }

  function ci(id) {
    if ( jQuery('#'+id).attr('checked') ) {
      icount++;
    } else {
      icount--;
    }
  }

  function divToggle(div) {
    div = '#'+div;
    jQuery(div).slideToggle(400);
  }

  function showCalendar(id) {
    if ( document.getElementById('date-no').checked ) {
      var id = '#'+id;
      jQuery(function() {
  		  jQuery(id).datepicker({
  		    dateFormat: 'yy-mm-dd',
  		    minDate: new Date(<?php echo date('Y, n - 1, j'); ?>),
  		    onClose: function() { jQuery(id).datepicker('destroy') }
  		  });
        jQuery(id).datepicker('show');
    	});
    } else {
      jQuery('#date-no').attr('checked', true);
      swTxt(cDate, false);
      showCalendar(id);
    }
  }

  function swChb(c, s) {
  	for ( i = 0; i < c.length; i++ ) {
  	  if ( s == true ) {
  	    jQuery('#'+c[i]).attr('checked', false);
  	  }
      jQuery('#'+c[i]).attr('disabled', s);
    }
  }

  function swTxt(c, s) {
  	for ( i = 0; i < c.length; i++ ) {
  	  if ( s == true ) {
  	    jQuery('#'+c[i]).val('');
  	  }
      jQuery('#'+c[i]).attr('disabled', s);
    }
  }

  var cAuthors = new Array(<?php echo implode(', ', $js_author_array); ?>);
  var cCat = new Array(<?php echo implode(', ', $js_category_array); ?>);
  var cRole = new Array(<?php echo implode(', ' , $jsroles); ?>);
  var cDate =  new Array('date_start', 'date_end');
  var icount = <?php echo $js_count; ?>;

  if ( jQuery('#role-yes').attr('checked') ) {
  	swChb(cRole, true);
  }
  if ( jQuery('#date-yes').attr('checked') ) {
  	swTxt(cDate, true);
  }
  if ( jQuery('#individual').attr('checked') ) {
    swChb(cAuthors, true);
    swChb(cCat, true);
  }

  jQuery(document).ready(function() {
		jQuery('#dynwid').accordion({
			header: 'h4',
			autoHeight: false,
		});
	});
</script>