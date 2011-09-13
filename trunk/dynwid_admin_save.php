<?php
/**
 * dynwid_admin_save.php - Saving options to the database
 *
 * @version $Id$
 * @copyright 2011 Jacco Drabbe
 */

  // Security - nonce
  check_admin_referer('plugin-name-action_edit_' . $_POST['widget_id']);

  /* Checking basic stuff */
  foreach ( $DW->overrule_maintype as $o ) {
  	$act_field = $o . '_act';
  	if ( $_POST[$o] == 'no' && count($_POST[$act_field]) == 0 ) {
  		wp_redirect( $_SERVER['REQUEST_URI'] . '&work=none' );
  		die();
  	}
  }

  // Date check
  if ( $_POST['date'] == 'no' ) {
    $date_start = trim($_POST['date_start']);
    $date_end = trim($_POST['date_end']);

    if (! preg_match('/^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}$/', $date_start) && ! preg_match('/^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}$/', $date_end) ) {
      wp_redirect( $_SERVER['REQUEST_URI'] . '&work=none' );
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
      if ( mktime(0, 0, 0, $date_start_month, $date_start_day, $date_start_year) > mktime(0, 0, 0, $date_end_month, $date_end_day, $date_end_year) ) {
        wp_redirect( $_SERVER['REQUEST_URI'] . '&work=nonedate' );
        die();
      }
    }
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
    if ( preg_match('/^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}$/', $date_start) ) {
      $dates['date_start'] = $date_start;
    }
    if ( preg_match('/^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}$/', $date_end) ) {
      $dates['date_end'] = $date_end;
    }

    if ( count($dates) > 0 ) {
      $DW->addDate($_POST['widget_id'], $dates);
    }
  }

  // Browser
	DWModule::save('browser', 'complex');

	// Template
	DWModule::save('tpl', 'complex');

  // Front Page
  DWModule::save('front-page');

  // Single Post
	DWModule::save('single');

  // -- Author
  if ( isset($_POST['single_author_act']) && count($_POST['single_author_act']) > 0 ) {
    if ( $_POST['single'] == 'yes' ) {
      $DW->addSingleOption($_POST['widget_id'], 'single', '1');
    }
    $DW->addMultiOption($_POST['widget_id'], 'single-author', $_POST['single'], $_POST['single_author_act']);
  }

  // -- Category
  if ( isset($_POST['single_category_act']) && count($_POST['single_category_act']) > 0 ) {
    if ( $_POST['single'] == 'yes' && count($_POST['single_author_act']) == 0 ) {
      $DW->addSingleOption($_POST['widget_id'], 'single', '1');
    }
    $DW->addMultiOption($_POST['widget_id'], 'single-category', $_POST['single'], $_POST['single_category_act']);
  }

  // -- Individual / Posts / Tag
  if ( isset($_POST['individual']) && $_POST['individual'] == '1' ) {
    $DW->addSingleOption($_POST['widget_id'], 'individual', '1');
    if ( count($_POST['single_post_act']) > 0 ) {
      $DW->addMultiOption($_POST['widget_id'], 'single-post', $_POST['single'], $_POST['single_post_act']);
    }
    if ( count($_POST['single_tag_act']) > 0 ) {
      $DW->addMultiOption($_POST['widget_id'], 'single-tag', $_POST['single'], $_POST['single_tag_act']);
    }
  }

  // Attachment
	DWModule::save('attachment');

  // Pages
	DWModule::save('page', 'complex');

  // -- Childs
  if ( isset($_POST['page_act']) && count($_POST['page_act']) > 0 && isset($_POST['page_childs_act']) && count($_POST['page_childs_act']) > 0 ) {
  	$DW->addChilds($_POST['widget_id'], 'page-childs', $_POST['page'], $_POST['page_act'], $_POST['page_childs_act']);
  }

  // Author
	DWModule::save('author', 'complex');

  // Categories
	DWModule::save('category', 'complex');

  // Archive
	DWModule::save('archive');

  // Error 404
	DWModule::save('e404');

  // Search
	DWModule::save('search');

  // Custom Types
  if ( isset($_POST['post_types']) ) {
    foreach ( $_POST['post_types'] as $type ) {
    	// Check taxonomies
    	$taxonomy = FALSE;

    	// Go throgh the tax_list - Workaround as for some reason get_object_taxonomies() is not always filled
    	$tax_list = array();
    	$len = strlen($type);
    	foreach ( $_POST['tax_list'] as $tl ) {
    		if ( substr($tl, 0, $len) == $type ) {
    			$tax_list [] = $tl;
    		}
    	}

    	// $tax_list = get_object_taxonomies($type);
    	foreach ( $tax_list as $tax ) {
    		$act_tax_field = $tax . '_act';
    		if ( isset($_POST[$act_tax_field]) && count($_POST[$act_tax_field]) > 0 ) {
    			$taxonomy = TRUE;
    			break;
    		}
    	}

      $act_field = $type . '_act';
      if ( count($_POST[$act_field]) > 0 || $taxonomy ) {
      	if (! is_array($_POST[$act_field]) ) {
      		$_POST[$act_field] = array();
      	}

        $DW->addMultiOption($_POST['widget_id'], $type, $_POST[$type], $_POST[$act_field]);
      } else if ( $_POST[$type] == 'no' ) {
        $DW->addSingleOption($_POST['widget_id'], $type);
      }

    	// -- Childs
    	$act_childs_field = $type . '_childs_act';
    	if ( count($_POST[$act_field]) > 0 && isset($_POST[$act_childs_field]) && count($_POST[$act_childs_field]) > 0 ) {
    		$DW->addChilds($_POST['widget_id'], $type . '-childs', $_POST[$type], $_POST[$act_field], $_POST[$act_childs_field]);
    	}

    	// -- Taxonomies
    	foreach ( $tax_list as $tax ) {
    		$act_tax_field = $tax . '_act';
    		if ( isset($_POST[$act_tax_field]) && count($_POST[$act_tax_field]) > 0 ) {
					$DW->addMultiOption($_POST['widget_id'], $tax, $_POST[$type], $_POST[$act_tax_field]);
    		}

    		// -- Childs
    		$act_tax_childs_field = $tax . '_childs_act';
    		if ( count($_POST[$act_tax_field]) > 0 && isset($_POST[$act_tax_childs_field]) && count($_POST[$act_tax_childs_field]) > 0 ) {
    			$DW->addChilds($_POST['widget_id'], $tax . '-childs', $_POST[$type], $_POST[$act_tax_field], $_POST[$act_tax_childs_field]);
    		}
    	}
    }

		DWModule::save('cp_archive', 'complex');
  }

	// Custom Taxonomies
	if ( isset($_POST['taxonomy']) ) {
		foreach ( $_POST['taxonomy'] as $tax ) {
			$type = 'tax_' . $tax;
			$act_field = $type . '_act';
			if ( isset($_POST[$act_field]) && count($_POST[$act_field]) > 0 ) {
				if (! is_array($_POST[$act_field]) ) {
					$_POST[$act_field] = array();
				}

				$DW->addMultiOption($_POST['widget_id'], $type, $_POST[$type], $_POST[$act_field]);
			} else if ( $_POST[$type] == 'no' ) {
				$DW->addSingleOption($_POST['widget_id'], $type);
			}

			$childs_act_field = $type . '_childs_act';
			if ( count($act_field) > 0 && isset($_POST[$childs_act_field]) && count($_POST[$childs_act_field]) > 0 ) {
				$DW->addChilds($_POST['widget_id'], $type . '-childs', $_POST[$type], $_POST[$act_field], $_POST[$childs_act_field]);
			}
		}
	}

  // WPML PLugin support
	DWModule::save('wpml', 'complex');

	// QTranslate Plugin support
	DWModule::save('qt', 'complex');

  // WPSC/WPEC Plugin support
	DWModule::save('wpsc', 'complex');

	// BP Plugin support
	DWModule::save('bp', 'complex');

	// BP Plugin support (Groups)
	DWModule::save('bp-group', 'complex');

	// Pods Plugin support
	DWModule::save('pods', 'complex');

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
        @list($name, $value) = explode('=', $arg);
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

    wp_redirect( $script_url . '?' . implode('&', $q) );
    die();
  }
?>