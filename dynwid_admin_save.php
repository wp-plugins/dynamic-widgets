<?php
/**
 * dynwid_admin_save.php - Saving options to the database
 *
 * @version $Id$
 */

  // Security - nonce
  check_admin_referer('plugin-name-action_edit_' . $_POST['widget_id']);

  require_once('dynwid_class.php');
  if (! isset($DW) ) {
    $DW = new dynWid();
  }

  // Checking basic stuff
  $fields = array( 'front-page', 'single', 'page', 'category', 'archive' );
  $work = FALSE;
  foreach ( $fields as $field ) {
    if ( $_POST[$field] == 'yes' ) {
      $work = TRUE;
      break;
    }
  }
  if (! $work ) {
    $fields = array( 'single_actor_act', 'single_category_act', 'page_act', 'category_act' );
    foreach ( $fields as $field ) {
      if ( count($_POST[$field]) > 0 ) {
        $work = TRUE;
        break;
      }
    }
  }
  if (! $work ) {
    wp_redirect( get_option('siteurl') . $_SERVER['REQUEST_URI'] . '&work=none' );
    die();
  }

  // Checking already set options
  if ( $DW->hasOptions($_POST['widget_id']) ) {
    $DW->resetOptions($_POST['widget_id']);
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

  // Pages
  if ( count($_POST['page_act']) > 0 ) {
    $DW->addMultiOption($_POST['widget_id'], 'page', $_POST['page'], $_POST['page_act']);
  } else if ( $_POST['page'] == 'no' ) {
    $DW->addSingleOption($_POST['widget_id'], 'page');
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
?>