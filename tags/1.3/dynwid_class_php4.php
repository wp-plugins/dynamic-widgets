<?php
/**
 * dynwid_class_php4.php - Dynamic Widgets Class for PHP4
 * Needs at least PHP 4.1.0
 *
 * @version $Id$
 */

  class dynWid {
    var $dbtable;                       /* private */
    var $dynwid_list;
    var $firstmessage;                  /* private */
    var $registered_sidebars;           /* private */
    var $registered_widget_controls;
    var $registered_widgets;
    var $sidebars;
    var $plugin_url;
    var $userrole;
    var $wpdb;                          /* private */

    /* Old constructor redirect to new constructor */
		function dynWid() {
			$this->__construct();
		}

    function __construct() {
      if ( is_user_logged_in() ) {
        $this->userrole = $GLOBALS['current_user']->roles;
      } else {
        $this->userrole = array('anonymous');
      }

      $this->firstmessage = TRUE;
      $this->registered_sidebars = $GLOBALS['wp_registered_sidebars'];
      $this->registered_widget_controls = &$GLOBALS['wp_registered_widget_controls'];
      $this->registered_widgets = &$GLOBALS['wp_registered_widgets'];
      $this->sidebars = wp_get_sidebars_widgets();
      $this->plugin_url = WP_PLUGIN_URL . '/' . str_replace( basename(__FILE__), '', plugin_basename(__FILE__) );

      $this->wpdb = $GLOBALS['wpdb'];
      $this->dbtable = $this->wpdb->prefix . DW_DB_TABLE;

      $this->createList();
    }

    function addDate($widget_id, $dates) {
      $query = "INSERT INTO " . $this->dbtable . "
                    (widget_id, maintype, name, value)
                  VALUES
                    ('" . $widget_id . "', 'date', 'default', '0')";
      $this->wpdb->query($query);

      foreach ( $dates as $name => $date ) {
        $query = "INSERT INTO " . $this->dbtable . "
                    (widget_id, maintype, name, value)
                  VALUES
                    ('" . $widget_id . "', 'date', '" . $name . "', '" . $date . "')";
        $this->wpdb->query($query);
      }
    }

    function addMultiOption($widget_id, $maintype, $default, $act) {
      if ( $default == 'no' ) {
        $opt_default = '0';
        $opt_act = '1';
      } else {
        $opt_default = '1';
        $opt_act = '0';
      }

      $query = "INSERT INTO " . $this->dbtable . "
                      (widget_id, maintype, name, value)
                    VALUES
                      ('" . $widget_id . "', '" . $maintype . "', 'default', '" . $opt_default . "')";
      $this->wpdb->query($query);
      foreach ( $act as $option ) {
        $query = "INSERT INTO " . $this->dbtable . "
                      (widget_id, maintype, name, value)
                    VALUES
                      ('" . $widget_id . "', '" . $maintype . "', '" . $option . "', '" . $opt_act . "')";
        $this->wpdb->query($query);
      }
    }

    function addSingleOption($widget_id, $maintype, $value = '0') {
      $query = "INSERT INTO " . $this->dbtable . "
                    (widget_id, maintype, value)
                  VALUES
                    ('" . $widget_id . "', '" . $maintype . "', '" . $value . "')";
      $this->wpdb->query($query);
    }

    /* private function */
    function createList() {
      $this->dynwid_list = array();

      foreach ( $this->sidebars as $sidebar_id => $widgets ) {
        if ( count($widgets) > 0 ) {
          foreach ( $widgets as $widget_id ) {
            if ( $this->hasOptions($widget_id) ) {
              $this->dynwid_list[ ] = $widget_id;
            }
          }
        }
      }
    }

    function deleteOption($widget_id, $maintype, $name = '') {
      $query = "DELETE FROM " . $this->dbtable . " WHERE widget_id = '" .$widget_id . "' AND maintype = '" .$maintype ."'";
      if (! empty($name) ) {
        $query .= " AND (name = '" . $name . "' OR name = 'default')";
      }
      $this->wpdb->query($query);
    }

    function detectPage() {
      if ( is_front_page() && get_option('show_on_front') == 'posts' ) {
        return 'front-page';
      } else if ( is_home() && get_option('show_on_front') == 'page' ) {
      	return 'home';
      } else if ( is_single() ) {
        return 'single';
      } else if ( is_page() ) {
        return 'page';
      } else if ( is_author() ) {
        return 'author';
      } else if ( is_category() ) {
        return 'category';
      } else if ( is_archive() && ! is_category() && ! is_author() ) {
        return 'archive';
      } else if ( is_404() ) {
      	return 'e404';
      } else if ( is_search() ) {
        return 'search';
      } else {
        return 'undef';
      }
    }

    function dump() {
    	echo "wp version: " . $GLOBALS['wp_version'] . "\n";
    	echo "dw version: " . DW_VERSION . "\n";
    	echo "php version: " . PHP_VERSION . "\n";
      echo "\n";
      echo "front: " . get_option('show_on_front') . "\n";
      if ( get_option('show_on_front') == 'page' ) {
        echo "front page: " . get_option('page_on_front') . "\n";
        echo "posts page: " . get_option('page_for_posts') . "\n";
      }

    	echo "\n";
      echo "list: \n";
      $list = array();
      foreach ( $this->dynwid_list as $widget_id ) {
        $list[$widget_id] = strip_tags($this->getName($widget_id));
      }
      print_r($list);

      echo "wp_registered_widgets: \n";
      print_r($this->registered_widgets);

      echo "options: \n";
      print_r( $this->getOptions('%', NULL) );

      echo "\n";
      echo serialize($this->getOptions('%', NULL));
    }

    function dumpOpt($opt) {
      if ( DW_DEBUG && count($opt) > 0 ) {
        echo '<pre>';
        print_r($opt);
        echo '</pre>';
      }
    }

    function getName($id, $type = 'W') {
      switch ( $type ) {
        case 'S':
          $lookup = $this->registered_sidebars;
        break;

        default:
          $lookup = $this->registered_widgets;
        // end default
      }

      $name = $lookup[$id]['name'];

      if ( $type == 'W' ) {
        // Retrieve optional set title
        $number = $lookup[$id]['params'][0]['number'];
        $option_name = $lookup[$id]['callback'][0]->option_name;
        $option = get_option($option_name);
        if (! empty($option[$number]['title']) ) {
          $name .= ': <span class="in-widget-title">' . $option[$number]['title'] . '</span>';
        }
      }

      return $name;
    }

    function getOptions($widget_id, $maintype, $admin = TRUE) {
      $opt = array();

      if ( $admin ) {
        $query = "SELECT widget_id, maintype, name, value FROM " . $this->dbtable . "
                  WHERE widget_id LIKE '" . $widget_id . "'
                    AND maintype LIKE '" . $maintype . "%'
                  ORDER BY maintype, name";

      } else {
      	if ( $maintype == 'home' ) {
      		$maintype = 'page';
      	}
        $query = "SELECT widget_id, maintype, name, value FROM " . $this->dbtable . "
                  WHERE widget_id LIKE '" . $widget_id . "'
                    AND (maintype LIKE '" . $maintype . "%' OR maintype = 'role' OR maintype = 'date')
                  ORDER BY maintype, name";
      }
      $results = $this->wpdb->get_results($query);

      foreach ( $results as $myrow ) {
        $opt[ ] = array('widget_id' => $myrow->widget_id,
                        'maintype' => $myrow->maintype,
                        'name' => $myrow->name,
                        'value' => $myrow->value
                  );
      }

      return $opt;
    }

    function hasOptions($widget_id) {
      $query = "SELECT COUNT(1) AS total FROM " . $this->dbtable . "
                  WHERE widget_id = '" . $widget_id . "' AND
                        maintype != 'individual'";
      $count = $this->wpdb->get_var($this->wpdb->prepare($query));

      if ( $count > 0 ) {
        return TRUE;
      } else {
        return FALSE;
      }
    }

    function message($text) {
      if ( DW_DEBUG ) {
        if ( $this->firstmessage ) {
          echo "\n";
          $this->firstmessage = FALSE;
        }
        echo '<!-- ' . $text . ' //-->';
        echo "\n";
      }
    }

    function resetOptions($widget_id) {
      $query = "DELETE FROM " . $this->dbtable . " WHERE widget_id = '" . $widget_id . "'";
      $this->wpdb->query($query);
    }
  }
?>