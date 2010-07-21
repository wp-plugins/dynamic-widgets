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

  // Date check
  if ( $_POST['date'] == 'no' ) {
    $date_start = trim($_POST['date_start']);
    $date_end = trim($_POST['date_end']);

    if (! ereg('^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}$', $date_start) && ! ereg('^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}$', $date_end) ) {
      wp_redirect( get_option('siteurl') . $_SERVER['REQUEST_URI'] . '&work=none' );
      die();
    }

    if (! empty($date_start) ) {
      @list($date_start_year, $date_start_month, $date_start_day ) = explode('-', $date_start);
      if (! checkdate($date_start_month, $date_start_day, $date_start_year) ) {
        unset($date_start);
      }
    }
    if (! empty($date_end) ) {
      @list($date_end_year, $date_end_month, $date_end_day ) = explode('-', $date_end);
      if (! checkdate($date_end_month, $date_end_day, $date_end_year) ) {
        unset($date_end);
      }
    }

    if (! empty($date_start) && ! empty($date_end) ) {
      if ( mktime(0,0,0,$date_start_month,$date_start_day,$date_start_year) > mktime(0,0,0,$date_end_month,$date_end_day, $date_end_year) ) {
        wp_redirect( get_option('siteurl') . $_SERVER['REQUEST_URI'] . '&work=nonedate' );
        die();
      }
    }
  }

  $fields = array('front-page', 'single', 'page', 'author', 'category', 'archive', 'e404', 'search');
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

  // Custom Types (WP >= 3.0)
  if ( version_compare($GLOBALS['wp_version'], '3.0', '>=') ) {
    if (! $work  ) {
      foreach ( $_POST['post_types'] as $type ) {
        if ( $_POST[$type] == 'yes' ) {
          $work = TRUE;
          break;
        }
      }
    }

    if (! $work ) {
      foreach ( $_POST['post_types'] as $type ) {
        $field = $type . '_act';
        if ( count($_POST[$field]) > 0 ) {
          $work = TRUE;
          break;
        }
      }
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

  // Date
  if ( $_POST['date'] == 'no' ) {
    $dates = array();
    if ( ereg('^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}$', $date_start) ) {
      $dates['date_start'] = $date_start;
    }
    if ( ereg('^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}$', $date_end) ) {
      $dates['date_end'] = $date_end;
    }

    if ( count($dates) > 0 ) {
      $DW->addDate($_POST['widget_id'], $dates);
    }
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

  // Search
  if ( $_POST['search'] == 'no' ) {
    $DW->addSingleOption($_POST['widget_id'], 'search');
  }

  // Custom Types (WP >= 3.0)
  if ( version_compare($GLOBALS['wp_version'], '3.0', '>=') ) {
    foreach ( $_POST['post_types'] as $type ) {
      $act_field = $type . '_act';
      if ( count($_POST[$act_field]) > 0 ) {
        $DW->addMultiOption($_POST['widget_id'], $type, $_POST[$type], $_POST[$act_field]);
      } else if ( $_POST[$type] == 'no' ) {
        $DW->addSingleOption($_POST['widget_id'], $type);
      }
    }
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