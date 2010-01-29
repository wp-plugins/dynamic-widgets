<?php
/**
 * dynwid_admin_edit.php - Options settinfs
 *
 * @version $Id$
 */

  // Front Page
  $frontpage_yes_selected = 'checked="true"';
  $opt_frontpage = $DW->getOptions($_GET['id'], 'front-page');
  if ( count($opt_frontpage) > 0 ) {
    $frontpage_condition = $opt_frontpage[0]['value'];
    if ( $frontpage_condition == '0' ) {
      $frontpage_no_selected = $frontpage_yes_selected;
      unset($frontpage_yes_selected);
    }
  }

  // Single Post
  $single_yes_selected = 'checked="true"';
  $opt_single = $DW->getOptions($_GET['id'], 'single');
  if ( count($opt_single) > 0 ) {
    $single_condition = $opt_single[0]['value'];
    if ( $single_condition == '0' ) {
      $single_no_selected = $single_yes_selected;
      unset($single_yes_selected);
    }
  }

  // -- Author
  $opt_single_author = $DW->getOptions($_GET['id'], 'single-author');
  if ( count($opt_single_author) > 0 ) {
    $single_author_act = array();
    foreach ( $opt_single_author as $single_author_condition ) {
      $single_author_act[ ] = $single_author_condition['name'];
    }
  }

  $authors = get_users_of_blog();
  if ( count($authors) > DW_LIST_LIMIT ) {
    $single_author_condition_select_style = 'style="overflow:auto;height:240px;"';
  }

  // -- Category
  $opt_single_category = $DW->getOptions($_GET['id'], 'single-category');
  if ( count($opt_single_category) > 0 ) {
    $single_category_act = array();
    foreach ( $opt_single_category as $single_category_condition ) {
      $single_category_act[ ] = $single_category_condition['name'];
    }
  }

  // Pages
  $page_yes_selected = 'checked="true"';
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
    $page_condition_select_style = 'style="overflow:auto;height:240px;"';
  }

  // Categories
  $category_yes_selected = 'checked="true"';
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
    $category_condition_select_style = 'style="overflow:auto;height:240px;"';
  }

  // Archives
  $archive_yes_selected = 'checked="true"';
  $opt_archive = $DW->getOptions($_GET['id'], 'archive');
  if ( count($opt_archive) > 0 ) {
    $archive_condition = $opt_archive[0]['value'];
    if ( $archive_condition == '0' ) {
      $archive_no_selected = $archive_yes_selected;
      unset($archive_yes_selected);
    }
  }

  // Error 404
  $e404_yes_selected = 'checked="true"';
  $opt_e404 = $DW->getOptions($_GET['id'], 'e404');
  if ( count($opt_e404) > 0 ) {
  	$e404_condition = $opt_e404[0]['value'];
  	if ( $e404_condition == '0' ) {
  		$e404_no_selected = $e404_yes_selected;
  		unset($e404_yes_selected);
  	}
  }
?>

<div id="adv"></div>

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
}
</style>

<script type="text/javascript">
  function tglinfo(id) {
    var element = document.getElementById(id);
    var display = 'd_';
    display = display.concat(id);

    if ( window[display] ) {
      element.style.display = 'none';
      window[display] = false;
    } else {
      element.style.display = 'inline';
      window[display] = true;
    }
  }
  var d_single = false;
  var d_archive = false;
</script>

<?php if ( $_POST['dynwid_save'] == 'yes' ) { ?>
<div class="updated fade" id="message">
  <p>
    <strong>Widget options saved.</strong> <a href="themes.php?page=dynwid-config">Return</a> to Dynamic Widgets overview.
  </p>
</div>
<?php } ?>

<?php if ( $_GET['work'] == 'none' ) { ?>
<div class="error" id="message">
  <p>Dynamic does not mean static hiding of a widget. Hint: <a href="widgets.php">Remove</a> the widget from the sidebar.</p>
</div>
<?php } ?>

<h3>Edit options for <em><?php echo $DW->getName($_GET['id']); ?></em> Widget</h3>

<form action="<?php echo attribute_escape($_SERVER['REQUEST_URI']); ?>" method="post">
<?php wp_nonce_field('plugin-name-action_edit_' . $_GET['id']); ?>
<input type="hidden" name="dynwid_save" value="yes" />
<input type="hidden" name="widget_id" value="<?php echo $_GET['id']; ?>" />

<b>Front Page</b><br />
Show widget on the front page?<br />
<input type="radio" name="front-page" value="yes" id="front-page-yes" <?php echo $frontpage_yes_selected; ?> /> <label for="front-page-yes">Yes</label>
<input type="radio" name="front-page" value="no" id="front-page-no" <?php echo $frontpage_no_selected; ?> /> <label for="front-page-no">No</label>

<br /><br />

<b>Single Posts</b> <img src="<?php echo $DW->plugin_url; ?>img/info.gif" alt="info" title="Click to toggle info" onclick="tglinfo('single')"><br />
Show widget on single posts?<br />
<div id="i_single">
<div id="single" class="infotext">
  When you use an author <b>AND</b> a category exception, both rules in the condition must be met. Otherwise the exception won't work.
  If you want to use the rules in an OR condition. Add the same widget again and apply the other rule to that.
</div>
</div>
<input type="radio" name="single" value="yes" id="single-yes" <?php echo $single_yes_selected; ?> /> <label for="single-yes">Yes</label>
<input type="radio" name="single" value="no" id="single-no" <?php echo $single_no_selected; ?> /> <label for="single-no">No</label><br />
<table border="0" cellspacing="0" cellpadding="0">
<tr>
  <td valign="top">
    Except the posts by author:
    <div id="single-author-select" class="condition-select" <?php echo $single_author_condition_select_style; ?>>
    <?php foreach ( $authors as $author ) { ?>
    <input type="checkbox" id="single_author_act_<?php echo $author->ID; ?>" name="single_author_act[]" value="<?php echo $author->ID; ?>" <?php if ( count($single_author_act) > 0 && in_array($author->ID,$single_author_act) ) { echo 'checked="true"'; } ?> /> <label for="single_author_act_<?php echo $author->ID; ?>"><?php echo $author->display_name; ?></label><br />
    <?php } ?>
    </div>
  </td>
  <td style="width:10px"></td>
  <td valign="top">
    Except the posts in category:
    <div id="single-category-select" class="condition-select" <?php echo $category_condition_select_style; ?>>
    <?php foreach ( $category as $cat ) { ?>
    <input type="checkbox" id="single_cat_act_<?php echo $cat->cat_ID; ?>" name="single_category_act[]" value="<?php echo $cat->cat_ID; ?>" <?php if ( count($single_category_act) > 0 && in_array($cat->cat_ID,$single_category_act) ) { echo 'checked="true"'; } ?> /> <label for="single_cat_act_<?php echo $cat->cat_ID; ?>"><?php echo $cat->name; ?></label><br />
    <?php } ?>
    </div>
  </td>
</tr>
</table>

<br /><br />

<b>Pages</b><br />
Show widget on pages?<br />
<input type="radio" name="page" value="yes" id="page-yes" <?php echo $page_yes_selected; ?> /> <label for="page-yes">Yes</label>
<input type="radio" name="page" value="no" id="page-no" <?php echo $page_no_selected; ?> /> <label for="page-no">No</label><br />
Except the pages:<br />
<div id="page-select" class="condition-select" <?php echo $page_condition_select_style; ?>>
<?php foreach ( $pages as $page ) { ?>
<input type="checkbox" id="page_act_<?php echo $page->ID; ?>" name="page_act[]" value="<?php echo $page->ID; ?>" <?php if ( count($page_act) > 0 && in_array($page->ID,$page_act) ) { echo 'checked="true"'; } ?> /> <label for="page_act_<?php echo $page->ID; ?>"><?php echo $page->post_title; ?></label><br />
<?php } ?>
</div>

<br />

<b>Category Pages</b><br />
Show widget on category pages?<br />
<input type="radio" name="category" value="yes" id="category-yes" <?php echo $category_yes_selected; ?> /> <label for="category-yes">Yes</label>
<input type="radio" name="category" value="no" id="category-no" <?php echo $category_no_selected; ?> /> <label for="category-no">No</label><br />
Except the categories:<br />
<div id="category-select" class="condition-select" <?php echo $category_condition_select_style; ?>>
<?php foreach ( $category as $cat ) { ?>
<input type="checkbox" id="cat_act_<?php echo $cat->cat_ID; ?>" name="category_act[]" value="<?php echo $cat->cat_ID; ?>" <?php if ( count($category_act) > 0 && in_array($cat->cat_ID,$category_act) ) { echo 'checked="true"'; } ?> /> <label for="cat_act_<?php echo $cat->cat_ID; ?>"><?php echo $cat->name; ?></label><br />
<?php } ?>
</div>

<br />

<b>Archive Pages</b> <img src="<?php echo $DW->plugin_url; ?>img/info.gif" alt="info" title="Click to toggle info" onclick="tglinfo('archive')"><br />
Show widget on archive pages?<br />
<div id="i_archive">
<div id="archive" class="infotext">
  This option does not include category pages.
</div>
</div>
<input type="radio" name="archive" value="yes" id="archive-yes" <?php echo $archive_yes_selected; ?> /> <label for="archive-yes">Yes</label>
<input type="radio" name="archive" value="no" id="archive-no" <?php echo $archive_no_selected; ?> /> <label for="archive-no">No</label>

<br /><br />

<b>'Not Found' Error (404) Page</b><br />
Show widget on error 404 page?<br />
<input type="radio" name="e404" value="yes" id="e404-yes" <?php echo $e404_yes_selected; ?> /> <label for="e404-yes">Yes</label>
<input type="radio" name="e404" value="no" id="e404-no" <?php echo $e404_no_selected; ?> /> <label for="e404-no">No</label>

<br /><br />

<input class="button-primary" type="submit" value="Save" />
</form>

<form action="themes.php" method="get">
<input type="hidden" name="page" value="dynwid-config" />
<input class="button-secondary" type="submit" value="Cancel" style="position:relative;top:-23px;left:80px;" />
</form>