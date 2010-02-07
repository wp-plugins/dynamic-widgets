<?php
/**
 * dynwid_worker.php - The worker does the actual work.
 *
 * @version $Id$
 */

  require_once('dynwid_class.php');
  if (! isset($DW) ) {
    $DW = new dynWid();
  }
  $DW->message('Dynamic Widgets INIT');
  $DW->message('User has role ' . $DW->userrole);

  $whereami = $DW->detectPage();

  foreach ( $DW->sidebars as $sidebar_id => $widgets ) {
    // Only processing active sidebars with widgets
    if ( $sidebar_id != 'wp_inactive_widgets' && count($widgets) > 0 ) {
      foreach ( $widgets as $widget_id ) {
        // Check if the widget has options set
        if ( in_array($widget_id,$DW->dynwid_list) ) {
          $act = array();
          $opt = $DW->getOptions($widget_id, $whereami, FALSE);
          $display = TRUE;
          $role = TRUE;

          foreach ( $opt as $condition ) {
            if ( empty($condition['name']) && $condition['value'] == '0' ) {
              $DW->message('Default for ' . $widget_id . ' set to FALSE (rule D1)');
              $display = FALSE;
              break;
            } else if ( $condition['maintype'] != 'role' ) {
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
            }
          }

          // Act the condition(s) when there are options set
          if ( count($opt) > 0 ) {
            // Check the role
            foreach ( $opt as $condition ) {
              if ( $condition['maintype'] == 'role' && $condition['name'] == $DW->userrole ) {
                $DW->message('Role sets display to TRUE (rule ER1)');
                $role = TRUE;
              }
            }

            switch ( $whereami ) {
              case 'single':
                global $post;

                $act_author = array();
                $act_category = array();
                $post_category = array();

                // Get the categories from the post
                $categories = get_the_category();
                foreach ( $categories as $category ) {
                  $post_category[ ] = $category->cat_ID;
                }

                // Split out the conditions
                foreach ( $opt as $condition ) {
                  if ( $condition['maintype'] == 'single-author' && $condition['name'] != 'default' ) {
                    $act_author[ ] = $condition['name'];
                  } else if ( $condition['maintype'] == 'single-category' && $condition['name'] != 'default' ) {
                    $act_category[ ] = $condition['name'];
                  }
                }

                if (! $display ) {
                  $other = TRUE;
                } else {
                  $other = FALSE;
                }

                if ( count($act_author) > 0 && count($act_category) > 0 ) {
                  // Use of array_intersect to be sure one value in both arrays returns true
                  if ( in_array($post->post_author,$act_author) && (bool) array_intersect($post_category, $act_category) ) {
                    $display = $other;
                    $DW->message('Exception triggered for ' . $widget_id . ' (rule ES1)');
                  }
                } else if ( count($act_author) > 0 && count($act_category == 0) ) {
                  if ( in_array($post->post_author,$act_author) ) {
                    $display = $other;
                    $DW->message('Exception triggered for ' . $widget_id . ' (rule ES2)');
                  }
                } else if ( count($act_author) == 0 && count($act_category) > 0 ) {
                  if ( (bool) array_intersect($post_category, $act_category) ) {
                    $display = $other;
                    $DW->message('Exception triggered for ' . $widget_id . ' (rule ES3)');
                  }
                }
                break;

              case 'page':
                if ( is_page($act) ) {
                  $display = $other;
                  $DW->message('Exception triggered for ' . $widget_id . ' (rule EP1)');
                }
                break;

              case 'category':
                if ( is_category($act) ) {
                  $display = $other;
                  $DW->message('Exception triggered for ' . $widget_id . ' (rule EC1)');
                }
                break;
            }
          }

          if (! $display || ! $role ) {
            $DW->message('Removed ' . $widget_id . ' from display');
            unset($DW->registered_widgets[$widget_id]);
          }
        }
      } // END foreach $widgets
    }
  } // END foreach $sidebars

  $DW->message('Dynamic Widgets END');
?>