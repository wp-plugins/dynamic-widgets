<?php
/**
 * dynwid_admin_edit.php - Options settings
 *
 * @version $Id$
 */

  // Roles
  $wp_roles = $GLOBALS['wp_roles'];
  $roles = array_merge($wp_roles->role_names, array('anonymous' => 'Anonymous|User role'));
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
  $single_condition == '1';
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

<?php if ( $_POST['dynwid_save'] == 'yes' ) { ?>
<div class="updated fade" id="message">
  <p>
    <strong>Widget options saved.</strong> <a href="themes.php?page=dynwid-config">Return</a> to Dynamic Widgets overview.
  </p>
</div>
<?php } else if ( $_GET['work'] == 'none' ) { ?>
<div class="error" id="message">
  <p>Dynamic does not mean static hiding of a widget. Hint: <a href="widgets.php">Remove</a> the widget from the sidebar.</p>
</div>
<?php } ?>

<h3>Edit options for <em><?php echo $DW->getName($_GET['id']); ?></em> Widget</h3>

<form action="<?php echo attribute_escape($_SERVER['REQUEST_URI']); ?>" method="post">
<?php wp_nonce_field('plugin-name-action_edit_' . $_GET['id']); ?>
<input type="hidden" name="dynwid_save" value="yes" />
<input type="hidden" name="widget_id" value="<?php echo $_GET['id']; ?>" />
<input type="hidden" name="returnurl" value="<?php echo urldecode($_GET['returnurl']); ?>" />

<b>Role</b> <img src="<?php echo $DW->plugin_url; ?>img/info.gif" alt="info" onclick="divToggle('role');" /><br />
Show widget to everybody?
<?php $DW->dumpOpt($opt_role); ?>
<div>
<div id="role" class="infotext">
  Setting options by role is very powerfull. It can override all other options!<br />
  Users who are not logged in, get the <em>Anonymous</em> role.
</div>
</div>
<input type="radio" name="role" value="yes" id="role-yes" <?php echo $role_yes_selected; ?> onclick="swChb(cRole, true);" /> <label for="role-yes">Yes</label>
<input type="radio" name="role" value="no" id="role-no" <?php echo $role_no_selected; ?> onclick="swChb(cRole, false)" /> <label for="role-no">No, only to:</label><br />
<div id="role-select" class="condition-select" <?php echo $role_condition_select_style; ?>>
<?php foreach ( $roles as $rid => $role ) { ?>
<input type="checkbox" id="role_act_<?php echo $rid; ?>" name="role_act[]" value="<?php echo $rid; ?>" <?php echo ( count($role_act) > 0 && in_array($rid,$role_act) ) ? 'checked="checked"' : ''; ?> /> <label for="role_act_<?php echo $rid; ?>"><?php echo $role; ?></label><br />
<?php } ?>
</div>

<br /><br />

<b>Front Page</b> <img src="<?php echo $DW->plugin_url; ?>img/info.gif" alt="info" onclick="divToggle('frontpage');" /><br />
Show widget on the front page?<br />
<?php $DW->dumpOpt($opt_frontpage); ?>
<div>
<div id="frontpage"  class="infotext">
	This option only applies when your front page is set to display your latest posts (See Settings &gt; Reading).<br />
	When a static page is set, you can use the options for the static pages below.
</div>
</div>
<input type="radio" name="front-page" value="yes" id="front-page-yes" <?php echo $frontpage_yes_selected; ?> /> <label for="front-page-yes">Yes</label>
<input type="radio" name="front-page" value="no" id="front-page-no" <?php echo $frontpage_no_selected; ?> /> <label for="front-page-no">No</label>

<br /><br />

<b>Single Posts</b> <img src="<?php echo $DW->plugin_url; ?>img/info.gif" alt="info" title="Click to toggle info" onclick="divToggle('single')" /><br />
Show widget default on single posts?<br />
<?php $DW->dumpOpt($opt_single); ?>
<div>
<div id="single" class="infotext">
  When you use an author <b>AND</b> a category exception, both rules in the condition must be met. Otherwise the exception rule won't be applied.
  If you want to use the rules in a logical OR condition. Add the same widget again and apply the other rule to that.
</div>
</div>
<input type="radio" name="single" value="yes" id="single-yes" <?php echo $single_yes_selected; ?> /> <label for="single-yes">Yes</label>
<input type="radio" name="single" value="no" id="single-no" <?php echo $single_no_selected; ?> /> <label for="single-no">No</label><br />
<?php $DW->dumpOpt($opt_individual); ?>
<input type="checkbox" id="individual" name="individual" value="1" <?php echo ( $individual ) ? 'checked="checked"' : ''; ?> onclick="chkInPosts()" />
<label for="individual">Make exception rule available to individual posts and tags. <?php echo $count_individual; ?></label>
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
    Except the posts by author:
    <?php $DW->dumpOpt($opt_single_author); ?>
    <div id="single-author-select" class="condition-select" <?php echo $author_condition_select_style; ?>>
    <?php foreach ( $authors as $author ) { ?>
    <?php $js_author_array[ ] = '\'single_author_act_' . $author->ID . '\''; ?>
    <input type="checkbox" id="single_author_act_<?php echo $author->ID; ?>" name="single_author_act[]" value="<?php echo $author->ID; ?>" <?php echo ( count($single_author_act) > 0 && in_array($author->ID,$single_author_act) ) ? 'checked="checked"' : '';  ?> onclick="ci('single_author_act_<?php echo $author->ID; ?>')" /> <label for="single_author_act_<?php echo $author->ID; ?>"><?php echo $author->display_name; ?></label><br />
    <?php } ?>
    </div>
  </td>
  <td style="width:10px"></td>
  <td valign="top">
    Except the posts in category:
    <?php $DW->dumpOpt($opt_single_category); ?>
    <div id="single-category-select" class="condition-select" <?php echo $category_condition_select_style; ?>>
    <?php foreach ( $category as $cat ) { ?>
    <?php $js_category_array[ ] = '\'single_cat_act_' . $cat->cat_ID . '\''; ?>
    <input type="checkbox" id="single_cat_act_<?php echo $cat->cat_ID; ?>" name="single_category_act[]" value="<?php echo $cat->cat_ID; ?>" <?php echo ( count($single_category_act) > 0 && in_array($cat->cat_ID,$single_category_act) ) ? 'checked="checked"' : ''; ?> onclick="ci('single_cat_act_<?php echo $cat->cat_ID; ?>')" /> <label for="single_cat_act_<?php echo $cat->cat_ID; ?>"><?php echo $cat->name; ?></label><br />
    <?php } ?>
    </div>
  </td>
</tr>
</table>

<br /><br />

<b>Pages</b><br />
Show widget default on static pages?<br />
<?php $DW->dumpOpt($opt_page); ?>
<input type="radio" name="page" value="yes" id="page-yes" <?php echo $page_yes_selected; ?> /> <label for="page-yes">Yes</label>
<input type="radio" name="page" value="no" id="page-no" <?php echo $page_no_selected; ?> /> <label for="page-no">No</label><br />
Except the page(s):<br />
<div id="page-select" class="condition-select" <?php echo $page_condition_select_style; ?>>
<?php foreach ( $pages as $page ) { ?>
<input type="checkbox" id="page_act_<?php echo $page->ID; ?>" name="page_act[]" value="<?php echo $page->ID; ?>" <?php echo ( count($page_act) > 0 && in_array($page->ID,$page_act) ) ? 'checked="checked"' : ''; ?> /> <label for="page_act_<?php echo $page->ID; ?>"><?php echo $page->post_title; ?> <?php echo ( get_option('show_on_front') == 'page' && isset($static_page[$page->ID]) ? '(' . $static_page[$page->ID] . ')' : '' ) ?></label><br />
<?php } ?>
</div>

<br /><br />

<b>Author Pages</b><br />
Show widget default on author pages?<br />
<?php $DW->dumpOpt($opt_author); ?>
<input type="radio" name="author" value="yes" id="author-yes" <?php echo $author_yes_selected; ?> /> <label for="author-yes">Yes</label>
<input type="radio" name="author" value="no" id="author-no" <?php echo $author_no_selected; ?> /> <label for="author-no">No</label><br />
Except the author(s):<br />
<div id="author-select" class="condition-select" <?php echo $author_condition_select_style; ?>>
<?php foreach ( $authors as $author ) { ?>
<input type="checkbox" id="author_act_<?php echo $author->ID; ?>" name="author_act[]" value="<?php echo $author->ID; ?>" <?php echo ( count($author_act) > 0 && in_array($author->ID,$author_act) ) ? 'checked="checked"' : ''; ?> /> <label for="author_act_<?php echo $author->ID; ?>"><?php echo $author->display_name; ?></label><br />
<?php } ?></div>

<br /><br />

<b>Category Pages</b><br />
Show widget default on category pages?<br />
<?php $DW->dumpOpt($opt_category); ?>
<input type="radio" name="category" value="yes" id="category-yes" <?php echo $category_yes_selected; ?> /> <label for="category-yes">Yes</label>
<input type="radio" name="category" value="no" id="category-no" <?php echo $category_no_selected; ?> /> <label for="category-no">No</label><br />
Except the categories:<br />
<div id="category-select" class="condition-select" <?php echo $category_condition_select_style; ?>>
<?php foreach ( $category as $cat ) { ?>
<input type="checkbox" id="cat_act_<?php echo $cat->cat_ID; ?>" name="category_act[]" value="<?php echo $cat->cat_ID; ?>" <?php echo ( count($category_act) > 0 && in_array($cat->cat_ID,$category_act) ) ? 'checked="checked"' : ''; ?> /> <label for="cat_act_<?php echo $cat->cat_ID; ?>"><?php echo $cat->name; ?></label><br />
<?php } ?>
</div>

<br /><br />

<b>Archive Pages</b> <img src="<?php echo $DW->plugin_url; ?>img/info.gif" alt="info" title="Click to toggle info" onclick="divToggle('archive')" /><br />
Show widget on archive pages?<br />
<?php $DW->dumpOpt($opt_archive); ?>
<div>
<div id="archive" class="infotext">
  This option does not include Author and Category Pages.
</div>
</div>
<input type="radio" name="archive" value="yes" id="archive-yes" <?php echo $archive_yes_selected; ?> /> <label for="archive-yes">Yes</label>
<input type="radio" name="archive" value="no" id="archive-no" <?php echo $archive_no_selected; ?> /> <label for="archive-no">No</label>

<br /><br />

<b>Error Page</b><br />
Show widget on the error page?<br />
<?php $DW->dumpOpt($opt_e404); ?>
<input type="radio" name="e404" value="yes" id="e404-yes" <?php echo $e404_yes_selected; ?> /> <label for="e404-yes">Yes</label>
<input type="radio" name="e404" value="no" id="e404-no" <?php echo $e404_no_selected; ?> /> <label for="e404-no">No</label>

<br /><br />

<input class="button-primary" type="submit" value="Save" />
</form>

<?php $url = (! empty($_GET['returnurl']) ) ? $_GET['returnurl'] : 'themes.php?page=dynwid-config'; ?>
<input class="button-secondary" type="submit" value="Return" style="position:relative;top:-23px;left:80px;" onclick="location.href='<?php echo $url; ?>'" />

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

  function swChb(c, s) {
  	for ( i = 0; i < c.length; i++ ) {
  	  if ( s == true ) {
  	    document.getElementById(c[i]).checked = false;
  	  }
      document.getElementById(c[i]).disabled = s;
    }
  }

  var cAuthors = new Array(<?php echo implode(', ', $js_author_array); ?>);
  var cCat = new Array(<?php echo implode(', ', $js_category_array); ?>);
  var cRole = new Array(<?php echo implode(', ' , $jsroles); ?>);
  var icount = <?php echo $js_count; ?>;

  if ( document.getElementById('role-yes').checked ) {
  	swChb(cRole, true);
  }
  if ( document.getElementById('individual').checked ) {
    swChb(cAuthors, true);
    swChb(cCat, true);
  }
</script>