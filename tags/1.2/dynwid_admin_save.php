<?php
/**
 * dynwid_admin_save.php - Saving options to the database
 *
 * @version $Id$
 */

  // Security - nonce
  check_admin_referer('plugin-name-action_edit_' . $_POST['widget_id']);

  // Checking basic stuff
  if ( $_POST['role'] == 'no' && count($_POST['role_act']) == 0 ) {
    wp_redirect( get_option('siteurl') . $_SERVER['REQUEST_URI'] . '&work=none' );
    die();
  }

  $fields = array('front-page', 'single', 'page', 'author', 'category', 'archive', 'e404');
  $work = FALSE;
  foreach ( $fields as $field ) {
    if ( $_POST[$field] == 'yes' ) {
      $work = TRUE;
      break;
    }
  }
  if (! $work ) {
    $fields = array('single_author_act',
                    'single_category_act',
                    'single_post_act',
                    'single_tag_act',
                    'page_act',
                    'author_act',
                    'category_act'
                 );
    foreach ( $fields as $field ) {
      if ( count($_POST[$field]) > 0 ) {
        $work = TRUE;
        break;
      }
    }
  }
  if (! $work) {
    if ( $_POST['individual'] == '1' ) {
      $work = TRUE;
    }
  }
  if (! $work ) {
    wp_redirect( get_option('siteurl') . $_SERVER['REQUEST_URI'] . '&work=none' );
    die();
  }

  // Removing already set options
  $DW->resetOptions($_POST['widget_id']);

  // Role
  if ( $_POST['role'] == 'no' && count($_POST['role_act']) > 0 ) {
    $DW->addMultiOption($_POST['widget_id'], 'role', 'no', $_POST['role_act']);
  }

  // Front Page
  if ( $_POST['front-page'] == 'no' ) {
    $DW->addSingleOption($_POST['widget_id'], 'front-page');
  }

  // Single Post
  if ( $_POST['single'] == 'no' ) {
    $DW->addSingleOption($_POST['widget_id'], 'single');
  }

  // -- Author
  if ( count($_POST['single_author_act']) > 0 ) {
    if ( $_POST['single'] == 'yes' ) {
      $DW->addSingleOption($_POST['widget_id'], 'single', '1');
    }
    $DW->addMultiOption($_POST['widget_id'], 'single-author', $_POST['single'], $_POST['single_author_act']);
  }

  // -- Category
  if ( count($_POST['single_category_act']) > 0 ) {
    if ( $_POST['single'] == 'yes' && count($_POST['single_author_act']) == 0 ) {
      $DW->addSingleOption($_POST['widget_id'], 'single', '1');
    }
    $DW->addMultiOption($_POST['widget_id'], 'single-category', $_POST['single'], $_POST['single_category_act']);
  }

  // -- Individual / Posts / Tag
  if ( $_POST['individual'] == '1' ) {
    $DW->addSingleOption($_POST['widget_id'], 'individual', '1');
    if ( count($_POST['single_post_act']) > 0 ) {
      $DW->addMultiOption($_POST['widget_id'], 'single-post', $_POST['single'], $_POST['single_post_act']);
    }
    if ( count($_POST['single_tag_act']) > 0 ) {
      $DW->addMultiOption($_POST['widget_id'], 'single-tag', $_POST['single'], $_POST['single_tag_act']);
    }
  }

  // Pages
  if ( count($_POST['page_act']) > 0 ) {
    $DW->addMultiOption($_POST['widget_id'], 'page', $_POST['page'], $_POST['page_act']);
  } else if ( $_POST['page'] == 'no' ) {
    $DW->addSingleOption($_POST['widget_id'], 'page');
  }

  // Author
  if ( count($_POST['author_act']) > 0 ) {
    $DW->addMultiOption($_POST['widget_id'], 'author', $_POST['author'], $_POST['author_act']);
  } else if ( $_POST['author'] == 'no' ) {
    $DW->addSingleOption($_POST['widget_id'], 'author');
  }

  // Categories
  if ( count($_POST['category_act']) > 0 ) {
    $DW->addMultiOption($_POST['widget_id'], 'category', $_POST['category'], $_POST['category_act']);
  } else if ( $_POST['category'] == 'no' ) {
    $DW->addSingleOption($_POST['widget_id'], 'category');
  }

  // Archive
  if ( $_POST['archive'] == 'no' ) {
    $DW->addSingleOption($_POST['widget_id'], 'archive');
  }

  // Error 404
  if ( $_POST['e404'] == 'no' ) {
  	$DW->addSingleOption($_POST['widget_id'], 'e404');
  }

  // Redirect to ReturnURL
  if (! empty($_POST['returnurl']) ) {
    $q = array();

    // Checking if there are arguments set
    $pos = strpos($_POST['returnurl'],'?');
    if ( $pos !== FALSE ) {
      // evaluate the args
      $query_string = substr($_POST['returnurl'], ($pos+1));
      $args = explode('&', $query_string);
      foreach ( $args as $arg ) {
        @list($name,$value) = explode('=', $arg);
        if ( $name != 'dynwid_save' && $name != 'widget_id' ) {
          $q[ ] = $name . '=' . $value;
        }
      }
      $script_url = substr($_POST['returnurl'],0,$pos);
    } else {
      $script_url = $_POST['returnurl'];
    }
    $q[ ] = 'dynwid_save=yes';
    $q[ ] = 'widget_id=' . $_POST['widget_id'];

    wp_redirect( get_option('siteurl') . $script_url . '?' . implode('&', $q) );
    die();
  }
?>