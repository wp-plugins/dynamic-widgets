<?php
/**
 * dynwid_worker.php - The worker does the actual work.
 *
 * @version $Id$
 */

  $DW->message('Dynamic Widgets INIT');
  $DW->message('User has role(s): ' . implode(', ', $DW->userrole));

  $whereami = $DW->detectPage();
  $DW->message('Page is ' . $whereami);
  if ( $whereami == 'single' ) {
    $post = $GLOBALS['post'];
    $DW->message('post_id = ' .$post->ID);
  }

  foreach ( $DW->sidebars as $sidebar_id => $widgets ) {
    // Only processing active sidebars with widgets
    if ( $sidebar_id != 'wp_inactive_widgets' && count($widgets) > 0 ) {
      foreach ( $widgets as $widget_id ) {
        // Check if the widget has options set
        if ( in_array($widget_id, $DW->dynwid_list) ) {
          $act = array();
          $opt = $DW->getOptions($widget_id, $whereami, FALSE);
          $DW->message('Number of rules to check for widget ' .$widget_id . ': ' . count($opt));
          $display = TRUE;
          $role = TRUE;
          $date = TRUE;

          foreach ( $opt as $condition ) {
            if ( empty($condition['name']) && $condition['value'] == '0' ) {
              $DW->message('Default for ' . $widget_id . ' set to FALSE (rule D1)');
              $display = FALSE;
              $other = TRUE;
              break;
            } else if ( $condition['maintype'] != 'role' && $condition['maintype'] != 'date' ) {
              // Get default value
              if ( $condition['name'] == 'default' ) {
                $default = $condition['value'];
              } else {
                $act[ ] = $condition['name'];
              }

              if ( $default == '0' ) {
                $DW->message('Default for ' . $widget_id . ' set to FALSE (rule D2)');
                $display = FALSE;
                $other = TRUE;
              } else {
                $DW->message('Default for ' . $widget_id . ' set to TRUE (rule D3)');
                $other = FALSE;
              }
            } else if ( $condition['maintype'] == 'role' && $condition['name'] == 'default' ) {
              $DW->message('Default for ' . $widget_id . ' set to FALSE (rule R1)');
              $role = FALSE;
            } else if ( $condition['maintype'] == 'date' && $condition['name'] == 'default' ) {
              $DW->message('Default for ' . $widget_id . ' set to FALSE (rule DT1)');
              $date = FALSE;
            }
          }

          // Act the condition(s) when there are options set
          if ( count($opt) > 0 ) {
            // Role exceptions
            foreach ( $opt as $condition ) {
							if ( $condition['maintype'] == 'role' && in_array($condition['name'], $DW->userrole) ) {
                $DW->message('Role set to TRUE (rule ER1)');
                $role = TRUE;
              }
            }

            // Date exceptions
            $dates = array();
            foreach ( $opt as $condition ) {
              if ( $condition['maintype'] == 'date' ) {
                switch ( $condition['name'] ) {
                  case 'date_start':
                    $date_start = $condition['value'];
                    break;

                  case 'date_end':
                    $date_end = $condition['value'];
                    break;
                }
              }
            }
            $now = time();
            if (! empty($date_end) ) {
              @list($date_end_year, $date_end_month, $date_end_day) = explode('-', $date_end);
              if ( mktime(23, 59, 59, $date_end_month, $date_end_day, $date_end_year) > $now ) {
                $date = TRUE;
                $DW->message('End date is in the future, sets Date to TRUE (rule EDT1)');
                if (! empty($date_start) ) {
                  @list($date_start_year, $date_start_month, $date_start_day) = explode('-', $date_start);
                  if ( mktime(0, 0, 0, $date_start_month, $date_start_day, $date_start_year) > $now ) {
                    $date = FALSE;
                    $DW->message('From date is in the future, sets Date to FALSE (rule EDT2)');
                  }
                }
              }
            } else if (! empty($date_start) ) {
              @list($date_start_year, $date_start_month, $date_start_day) = explode('-', $date_start);
              if ( mktime(0, 0, 0, $date_start_month, $date_start_day, $date_start_year) < $now ) {
                $date = TRUE;
                $DW->message('From date is in the past, sets Date to TRUE (rule EDT3)');
              }
            }

            // For debug messages
            $e = ( $other ) ? 'TRUE' : 'FALSE';

            // Display exceptions
            switch ( $whereami ) {
              case 'single':
                $act_author = array();
                $act_category = array();
                $act_post = array();
                $act_tag = array();
                $post_category = array();
                $post_tag = array();

                // Get the categories from the post
                $categories = get_the_category();
                foreach ( $categories as $category ) {
                  $post_category[ ] = $category->cat_ID;
                }

                // Get the tags form the post
                if ( has_tag() ) {
                  $tags = get_the_tags();
                  foreach ( $tags as $tag ) {
                    $post_tag[ ] = $tag->term_id;
                  }
                } else {
                  $tags = array();
                }

                // Split out the conditions
                foreach ( $opt as $condition ) {
                  if ( $condition['name'] != 'default' ) {
                    switch ( $condition['maintype'] ) {
                      case 'single-author':
                        $act_author[ ] = $condition['name'];
                      break;

                      case 'single-category':
                        $act_category[ ] = $condition['name'];
                      break;

                      case 'single-tag':
                        $act_tag[ ] = $condition['name'];
                      break;

                      case 'single-post':
                        $act_post[ ] = $condition['name'];
                      break;
                    } // END switch
                  }
                }

                /* Author AND Category */
                if ( count($act_author) > 0 && count($act_category) > 0 ) {
                  // Use of array_intersect to be sure one value in both arrays returns true
                  if ( in_array($post->post_author, $act_author) && (bool) array_intersect($post_category, $act_category) ) {
                    $display = $other;
                    $DW->message('Exception triggered for ' . $widget_id . ' sets display to ' . $e . ' (rule ES1)');
                  }
                /* Only Author */
                } else if ( count($act_author) > 0 && count($act_category == 0) ) {
                  if ( in_array($post->post_author, $act_author) ) {
                    $display = $other;
                    $DW->message('Exception triggered for ' . $widget_id . ' sets display to ' . $e . ' (rule ES2)');
                  }
                /* Only Category */
                } else if ( count($act_author) == 0 && count($act_category) > 0 ) {
                  if ( (bool) array_intersect($post_category, $act_category) ) {
                    $display = $other;
                    $DW->message('Exception triggered for ' . $widget_id . ' sets display to ' . $e . ' (rule ES3)');
                  }
                /* None or individual checked - individual is not included in the $opt */
                } else {
                  /* Tags */
                  if ( count($act_tag) > 0 ) {
                    if ( (bool) array_intersect($post_tag, $act_tag) ) {
                      $display = $other;
                      $DW->message('Exception triggered for ' . $widget_id . ' sets display to ' . $e . ' (rule ES4)');
                    }
                  }
                  /* Posts */
                  if ( count($act_post) > 0 ) {
                    if ( in_array($post->ID,$act_post) ) {
                      $display = $other;
                      $DW->message('Exception triggered for ' . $widget_id . ' sets display to ' . $e . ' (rule ES5)');
                    }
                  }
                }
                break;

              case 'home':
              	if ( count($act) > 0 ) {
              		$home_id = get_option('page_for_posts');
              		if ( in_array($home_id, $act) ) {
              			$display = $other;
              			$DW->message('Exception triggered for ' . $widget_id . ' sets display to ' . $e . ' (rule EH1)');
              		}
              	}
              	break;

              case 'page':
                if ( count($act) > 0 && is_page($act) ) {
                  $display = $other;
                  $DW->message('Exception triggered for ' . $widget_id . ' sets display to ' . $e . ' (rule EP1)');
                }
                break;

              case 'author':
                if ( count($act) > 0 && is_author($act) ) {
                  $display = $other;
                  $DW->message('Exception triggered for ' . $widget_id . ' sets display to ' . $e . ' (rule EA1)');
                }
                break;

              case 'category':
                if ( count($act) > 0 && is_category($act) ) {
                  $display = $other;
                  $DW->message('Exception triggered for ' . $widget_id . ' sets display to ' . $e . ' (rule EC1)');
                }
                break;
            } // END switch ( $whereami )
          } /* END if ( count($opt) > 0 ) */

          if (! $display || ! $role || ! $date ) {
            $DW->message('Removed ' . $widget_id . ' from display');
            unset($DW->registered_widgets[$widget_id]);
          }
        } // END if ( in_array($widget_id, $DW->dynwid_list) )
      } // END foreach ( $widgets as $widget_id )
    } // END if ( $sidebar_id != 'wp_inactive_widgets' && count($widgets) > 0 )
  } // END foreach ( $DW->sidebars as $sidebar_id => $widgets )

  $DW->message('Dynamic Widgets END');
?>