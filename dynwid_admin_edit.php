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
  if ( get_option('show_on_front') == 'page' ) {
    $frontpage_yes_selected = 'disabled="true"';
    $frontpage_no_selected = $frontpage_yes_selected;
  } else {
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
      if ( $page_condition['name'] == 'default' || empty($page_condition['name']) ) {
        $page_default = $page_condition['value'];
      } else {
        $page_act[ ] = $page_condition['name'];
      }
    }

    if ( $page_default == '0' ) {
      $page_no_selected = $page_yes_selected;
      unset($page_yes_selected);
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
</style>

<?php if ( isset($_POST['dynwid_save']) && $_POST['dynwid_save'] == 'yes' ) { ?>
<div class="updated fade" id="message">
  <p>
    <strong><?php _e('Widget options saved.'); ?></strong> <a href="themes.php?page=dynwid-config">Return</a> to Dynamic Widgets overview.
  </p>
</div>
<?php } else if ( isset($_GET['work']) && $_GET['work'] == 'none' ) { ?>
<div class="error" id="message">
  <p>Dynamic does not mean static hiding of a widget. Hint: <a href="widgets.php">Remove</a> the widget from the sidebar.</p>
</div>
<?php } else if ( isset($_GET['work']) && $_GET['work'] == 'nonedate' ) { ?>
<div class="error" id="message">
  <p>The From date can't be later than the To date.</p>
</div>

<?php } ?>

<h3><?php _e('Edit options for', DW_L10N_DOMAIN); ?> <em><?php echo $DW->getName($_GET['id']); ?></em> <?php _e('Widget'); ?></h3>
<?php echo ( DW_DEBUG ) ? '<pre>ID = ' . $_GET['id'] . '</pre><br />' : ''; ?>

<form action="<?php echo trailingslashit(admin_url()) . 'themes.php?page=dynwid-config&action=edit&id=' . $_GET['id']; ?>" method="post">
<?php wp_nonce_field('plugin-name-action_edit_' . $_GET['id']); ?>
<input type="hidden" name="dynwid_save" value="yes" />
<input type="hidden" name="widget_id" value="<?php echo $_GET['id']; ?>" />
<input type="hidden" name="returnurl" value="<?php echo ( isset($_GET['returnurl']) ? urldecode($_GET['returnurl']) : '' ); ?>" />

<b><?php _e('Role'); ?></b> <img src="<?php echo $DW->plugin_url; ?>img/info.gif" alt="info" onclick="divToggle('role');" /><br />
<?php _e('Show widget to everybody?', DW_L10N_DOMAIN); ?><br />
<?php $DW->dumpOpt($opt_role); ?>
<div>
<div id="role" class="infotext">
  Setting options by role is very powerfull. It can override all other options!<br />
  Users who are not logged in, get the <em>Anonymous</em> role.
</div>
</div>
<input type="radio" name="role" value="yes" id="role-yes" <?php echo ( isset($role_yes_selected) ? $role_yes_selected : '' ); ?> onclick="swChb(cRole, true);" /> <label for="role-yes"><?php _e('Yes'); ?></label>
<input type="radio" name="role" value="no" id="role-no" <?php echo ( isset($role_no_selected) ? $role_no_selected : '' ); ?> onclick="swChb(cRole, false)" /> <label for="role-no"><?php _e('No'); ?>, <?php _e('only to', DW_L10N_DOMAIN); ?>:</label><br />
<div id="role-select" class="condition-select" <?php echo ( isset($role_condition_select_style) ? $role_condition_select_style : '' ); ?>>
<?php foreach ( $roles as $rid => $role ) { ?>
<input type="checkbox" id="role_act_<?php echo $rid; ?>" name="role_act[]" value="<?php echo $rid; ?>" <?php echo ( isset($role_act) && count($role_act) > 0 && in_array($rid, $role_act) ) ? 'checked="checked"' : ''; ?> /> <label for="role_act_<?php echo $rid; ?>"><?php echo $role; ?></label><br />
<?php } ?>
</div>

<br />

<b><?php _e('Date'); ?></b> <img src="<?php echo $DW->plugin_url; ?>img/info.gif" alt="info" onclick="divToggle('date');" /><br />
<?php _e('Show widget always?', DW_L10N_DOMAIN); ?><br />
<?php $DW->dumpOpt($opt_date); ?>
<div>
<div id="date" class="infotext">
  Next to the above role option, the date option is also very powerfull. You've been warned!<br />
  Enter dates in the YYYY-MM-DD format. You can also use the calender by clicking on the <img src="<?php echo $DW->plugin_url; ?>img/calendar.gif" alt="Calendar" /><br />
  Date ranges can be made by entering a From AND a To date<br />
  When you want the widget to be displayed from a specific date, only fill in the From date<br />
  When you want the widget to stop displaying on a specific date, only fill in the To date.
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

<br />

<b><?php _e('Front Page', DW_L10N_DOMAIN); ?></b> <img src="<?php echo $DW->plugin_url; ?>img/info.gif" alt="info" onclick="divToggle('frontpage');" /><br />
<?php _e('Show widget on the front page?', DW_L10N_DOMAIN) ?><br />
<?php $DW->dumpOpt($opt_frontpage); ?>
<div>
<div id="frontpage"  class="infotext">
	This option only applies when your front page is set to display your latest posts (See Settings &gt; Reading).<br />
	When a static page is set, you can use the options for the static pages below.
</div>
</div>
<input type="radio" name="front-page" value="yes" id="front-page-yes" <?php echo ( isset($frontpage_yes_selected) ? $frontpage_yes_selected : '' ); ?> /> <label for="front-page-yes"><?php _e('Yes'); ?></label>
<input type="radio" name="front-page" value="no" id="front-page-no" <?php echo ( isset($frontpage_no_selected) ? $frontpage_no_selected : '' ); ?> /> <label for="front-page-no"><?php _e('No'); ?></label>

<br /><br />

<b><?php _e('Single Posts'); ?></b> <img src="<?php echo $DW->plugin_url; ?>img/info.gif" alt="info" title="Click to toggle info" onclick="divToggle('single')" /><br />
<?php _e('Show widget default on single posts?', DW_L10N_DOMAIN) ?><br />
<?php $DW->dumpOpt($opt_single); ?>
<div>
<div id="single" class="infotext">
  When you use an author <b>AND</b> a category exception, both rules in the condition must be met. Otherwise the exception rule won't be applied.
  If you want to use the rules in a logical OR condition. Add the same widget again and apply the other rule to that.
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
  When you enable this option, you have the ability to apply the exception rule for <em>Single Posts</em> to tags and individual posts. You can set the exception rule for tags in the single Edit Tag Panel (go to <a href="edit-tags.php?taxonomy=post_tag">Post Tags</a>, click a tag), For individual posts in the <a href="post-new.php">New</a> or <a href="edit.php">Edit</a> Posts panel. Exception rules for tags and individual posts in any combination work independantly, but will always be counted as one exception.<br />
  Please note when exception rules are set for Author and/or Category, these will be removed.
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

<br /><br />

<b><?php _e('Pages'); ?></b> <?php echo ( $DW->wpml ? $wpml_icon : '' ); ?><br />
<?php _e('Show widget default on static pages?', DW_L10N_DOMAIN); ?><br />
<?php $DW->dumpOpt($opt_page); ?>
<input type="radio" name="page" value="yes" id="page-yes" <?php echo ( isset($page_yes_selected) ? $page_yes_selected : '' ); ?> /> <label for="page-yes"><?php _e('Yes'); ?></label>
<input type="radio" name="page" value="no" id="page-no" <?php echo ( isset($page_no_selected) ? $page_no_selected : '' ); ?> /> <label for="page-no"><?php _e('No'); ?></label><br />
<?php _e('Except the page(s)', DW_L10N_DOMAIN); ?>:<br />
<div id="page-select" class="condition-select" <?php echo ( isset($page_condition_select_style) ? $page_condition_select_style : '' ); ?>>
<?php foreach ( $pages as $page ) { ?>
<input type="checkbox" id="page_act_<?php echo $page->ID; ?>" name="page_act[]" value="<?php echo $page->ID; ?>" <?php echo ( isset($page_act) && count($page_act) > 0 && in_array($page->ID,$page_act) ) ? 'checked="checked"' : ''; ?> /> <label for="page_act_<?php echo $page->ID; ?>"><?php echo $page->post_title; ?> <?php echo ( get_option('show_on_front') == 'page' && isset($static_page[$page->ID]) ? '(' . $static_page[$page->ID] . ')' : '' ) ?></label><br />
<?php } ?>
</div>

<br /><br />

<b><?php _e('Author Pages', DW_L10N_DOMAIN); ?></b><br />
<?php _e('Show widget default on author pages?', DW_L10N_DOMAIN); ?><br />
<?php $DW->dumpOpt($opt_author); ?>
<input type="radio" name="author" value="yes" id="author-yes" <?php echo ( isset($author_yes_selected) ? $author_yes_selected : '' ); ?> /> <label for="author-yes"><?php _e('Yes'); ?></label>
<input type="radio" name="author" value="no" id="author-no" <?php echo ( isset($author_no_selected) ? $author_no_selected : '' ); ?> /> <label for="author-no"><?php _e('No'); ?></label><br />
<?php _e('Except the author(s)', DW_L10N_DOMAIN); ?>:<br />
<div id="author-select" class="condition-select" <?php echo ( isset($author_condition_select_style) ? $author_condition_select_style : '' ); ?>>
<?php foreach ( $authors as $author ) { ?>
<input type="checkbox" id="author_act_<?php echo $author->ID; ?>" name="author_act[]" value="<?php echo $author->ID; ?>" <?php echo ( isset($author_act) && count($author_act) > 0 && in_array($author->ID,$author_act) ) ? 'checked="checked"' : ''; ?> /> <label for="author_act_<?php echo $author->ID; ?>"><?php echo $author->display_name; ?></label><br />
<?php } ?></div>

<br /><br />

<b><?php _e('Category Pages', DW_L10N_DOMAIN); ?></b> <?php echo ( $DW->wpml ? $wpml_icon : '' ); ?><br />
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

<br /><br />

<b><?php _e('Archive Pages', DW_L10N_DOMAIN); ?></b> <img src="<?php echo $DW->plugin_url; ?>img/info.gif" alt="info" title="Click to toggle info" onclick="divToggle('archive')" /><br />
<?php _e('Show widget on archive pages', DW_L10N_DOMAIN); ?>?<br />
<?php $DW->dumpOpt($opt_archive); ?>
<div>
<div id="archive" class="infotext">
  <?php _e('This option does not include Author and Category Pages.'); ?>
</div>
</div>
<input type="radio" name="archive" value="yes" id="archive-yes" <?php echo ( isset($archive_yes_selected) ? $archive_yes_selected : '' ); ?> /> <label for="archive-yes"><?php _e('Yes'); ?></label>
<input type="radio" name="archive" value="no" id="archive-no" <?php echo ( isset($archive_no_selected) ? $archive_no_selected : '' ); ?> /> <label for="archive-no"><?php _e('No'); ?></label>

<br /><br />

<b><?php _e('Error Page', DW_L10N_DOMAIN); ?></b><br />
<?php _e('Show widget on the error page?', DW_L10N_DOMAIN); ?><br />
<?php $DW->dumpOpt($opt_e404); ?>
<input type="radio" name="e404" value="yes" id="e404-yes" <?php echo ( isset($e404_yes_selected) ? $e404_yes_selected : '' ); ?> /> <label for="e404-yes"><?php _e('Yes'); ?></label>
<input type="radio" name="e404" value="no" id="e404-no" <?php echo ( isset($e404_no_selected) ? $e404_no_selected : '' ); ?> /> <label for="e404-no"><?php _e('No'); ?></label>

<br /><br />

<b><?php _e('Search Page', DW_L10N_DOMAIN); ?></b><br />
<?php _e('Show widget on the search page?', DW_L10N_DOMAIN); ?><br />
<?php $DW->dumpOpt($opt_search); ?>
<input type="radio" name="search" value="yes" id="search-yes" <?php echo ( isset($search_yes_selected) ? $search_yes_selected : '' ); ?> /> <label for="search-yes"><?php _e('Yes'); ?></label>
<input type="radio" name="search" value="no" id="search-no" <?php echo ( isset($search_no_selected) ? $search_no_selected : '' ); ?> /> <label for="search-no"><?php _e('No'); ?></label>

<br /><br />

<?php
  /* WordPress 3.0 and higher: Custom Post Types */
  if ( version_compare($GLOBALS['wp_version'], '3.0', '>=') ) {
    $args = array(
              'public'   => TRUE,
              '_builtin' => FALSE
            );
    $post_types = get_post_types($args, 'objects', 'and');

    foreach ( $post_types as $ctid ) {
      // Prepare
      $custom_yes_selected = 'checked="checked"';
      $opt_custom = $DW->getOptions($_GET['id'], key($post_types));
      if ( count($opt_custom) > 0 ) {
        $custom_act = array();
        foreach ( $opt_custom as $custom_condition ) {
          if ( $custom_condition['name'] == 'default' || empty($custom_condition['name']) ) {
            $custom_default = $custom_condition['value'];
          } else {
            $custom_act[ ] = $custom_condition['name'];
          }
        }

        if ( $custom_default == '0' ) {
          $custom_no_selected = $custom_yes_selected;
          unset($custom_yes_selected);
        }
      }

      $loop = new WP_Query( array('post_type' => key($post_types)) );
      if ( $loop->post_count > DW_LIST_LIMIT ) {
        $custom_condition_select_style = DW_LIST_STYLE;
      }

      // Output
      echo '<input type="hidden" name="post_types[]" value="' . key($post_types) . '" />';
      echo '<b>' . __('Custom Post Type') . ' <em>' . $ctid->label . '</em></b> ' . ( $DW->wpml ? $wpml_icon : '' ) . '<br />';
      echo __('Show widget on', DW_L10N_DOMAIN) . ' ' . $ctid->label . '?<br />';
      $DW->dumpOpt($opt_custom);
      echo '<input type="radio" name="' . key($post_types) . '" value="yes" id="' . key($post_types) . '-yes" ' . ( isset($custom_yes_selected) ? $custom_yes_selected : '' ) . ' /> <label for="' . key($post_types) . '-yes">' . __('Yes') . '</label> ';
      echo '<input type="radio" name="' . key($post_types) . '" value="no" id="' . key($post_types) . '-no" ' . ( isset($custom_no_selected) ? $custom_no_selected : '' ) . ' /> <label for="' . key($post_types) . '-no">' . __('No') . '</label><br />';

      echo __('Except for') . ':<br />';
      echo '<div id="' . key($post_types) . '-select" class="condition-select" ' . ( isset($custom_condition_select_style) ? $custom_condition_select_style : '' ) . '>';

      while ( $loop->have_posts() ) : $loop->the_post();
        echo '<input type="checkbox" id="' . key($post_types) . '_act_' . $loop->post->ID . '" name="' . key($post_types) . '_act[]" value="' . $loop->post->ID . '" ';
        echo ( count($custom_act) > 0 && in_array($loop->post->ID,$custom_act) ) ? 'checked="checked"' : '';
        echo ' /> <label for="' . key($post_types) . '_act_' . $loop->post->ID . '">';
        the_title();
        echo '</label><br />';
      endwhile;
      echo '</div>';

      echo '<br /><br />';
    }
  } // end version compare >= WP 3.0

  if ( $DW->wpsc ) {
?>
<b><?php _e('WPSC Category', DW_L10N_DOMAIN); ?></b><br />
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
<br /><br />
<?php
  } // DW->wpsc
?>

<input class="button-primary" type="submit" value="<?php _e('Save'); ?>" />
</form>

<?php $url = (! empty($_GET['returnurl']) ) ? urldecode($_GET['returnurl']) : trailingslashit(admin_url()) . 'themes.php?page=dynwid-config'; ?>
<input class="button-secondary" type="button" value="<?php _e('Return', DW_L10N_DOMAIN); ?>" style="position:relative;top:-23px;left:80px;" onclick="location.href='<?php echo $url; ?>'" />

<script type="text/javascript">
  function chkInPosts() {
    var posts = <?php echo count($single_post_act); ?>;
    var tags = <?php echo count($single_tag_act); ?>;

    if ( (posts > 0 || tags > 0) && document.getElementById('individual').checked == false ) {
      if ( confirm('Are you sure you want to disable the exception rule for individual posts and tags?\nThis will remove the options set to individual posts and/or tags for this widget.\nOk = Yes; No = Cancel') ) {
        swChb(cAuthors, false);
        swChb(cCat, false);
      } else {
        document.getElementById('individual').checked = true;
      }
    } else if ( icount > 0 && document.getElementById('individual').checked ) {
      if ( confirm('Are you sure you want to enable the exception rule for individual posts and tags?\nThis will remove the exceptions set for Author and/or Category on single posts for this widget.\nOk = Yes; No = Cancel') ) {
        swChb(cAuthors, true);
        swChb(cCat, true);
        icount = 0;
      } else {
        document.getElementById('individual').checked = false;
      }
    } else if ( document.getElementById('individual').checked ) {
        swChb(cAuthors, true);
        swChb(cCat, true);
    } else {
        swChb(cAuthors, false);
        swChb(cCat, false);
    }
  }

  function ci(id) {
    if ( document.getElementById(id).checked ) {
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
      document.getElementById('date-no').checked = true;
      swTxt(cDate, false);
      showCalendar(id);
    }
  }

  function swChb(c, s) {
  	for ( i = 0; i < c.length; i++ ) {
  	  if ( s == true ) {
  	    document.getElementById(c[i]).checked = false;
  	  }
      document.getElementById(c[i]).disabled = s;
    }
  }

  function swTxt(c, s) {
  	for ( i = 0; i < c.length; i++ ) {
  	  if ( s == true ) {
  	    document.getElementById(c[i]).value = '';
  	  }
      document.getElementById(c[i]).disabled = s;
    }
  }

  var cAuthors = new Array(<?php echo implode(', ', $js_author_array); ?>);
  var cCat = new Array(<?php echo implode(', ', $js_category_array); ?>);
  var cRole = new Array(<?php echo implode(', ' , $jsroles); ?>);
  var cDate =  new Array('date_start', 'date_end');
  var icount = <?php echo $js_count; ?>;

  if ( document.getElementById('role-yes').checked ) {
  	swChb(cRole, true);
  }
  if ( document.getElementById('date-yes').checked ) {
  	swTxt(cDate, true);
  }
  if ( document.getElementById('individual').checked ) {
    swChb(cAuthors, true);
    swChb(cCat, true);
  }
</script>