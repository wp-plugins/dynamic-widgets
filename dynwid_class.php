<?
/**
 * dynwid_class.php - Dynamic Widgets Class
 *
 * @version $Id$
 */

  class dynWid {
    private $dbtable;
    public  $dynwid_list;
    private $firstmessage;
    private $registered_sidebars;
    public  $registered_widgets;
    public  $sidebars;
    public  $plugin_url;
    private $wpdb;

    public function __construct() {
      global $wp_registered_sidebars, $wp_registered_widgets, $wpdb;
      // $wp_registered_widget_controls

      $this->firstmessage = TRUE;
      $this->registered_sidebars = $wp_registered_sidebars;
      $this->registered_widgets = &$wp_registered_widgets;
      $this->sidebars = wp_get_sidebars_widgets();
      $this->plugin_url = WP_PLUGIN_URL . '/' . str_replace( basename( __FILE__), '', plugin_basename(__FILE__) );

      $this->wpdb = $wpdb;
      $this->dbtable = $this->wpdb->prefix . DW_DB_TABLE;

      $this->createList();
    }

    public function addMultiOption($widget_id, $maintype, $default, $act) {
      if ( $default == 'no' ) {
        $opt_default = '0';
        $opt_act = '1';
      } else {
        $opt_default = '1';
        $opt_act = '0';
      }

      $query = "INSERT INTO " . $this->dbtable . "
                      (widget_id,maintype,name,value)
                    VALUES
                      ('" . $widget_id . "', '" . $maintype . "', 'default', '" . $opt_default . "')";
      $this->wpdb->query($query);
      foreach ( $act as $option ) {
        $query = "INSERT INTO " . $this->dbtable . "
                      (widget_id,maintype,name,value)
                    VALUES
                      ('" . $widget_id . "', '" . $maintype . "', '" . $option . "', '" . $opt_act . "')";
        $this->wpdb->query($query);
      }
    }

    public function addSingleOption($widget_id, $maintype, $value = '0') {
      $query = "INSERT INTO " . $this->dbtable . "
                    (widget_id,maintype,value)
                  VALUES
                    ('" . $widget_id . "', '" . $maintype . "', '" . $value . "')";
      $this->wpdb->query($query);
    }

    private function createList() {
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

    public function detectPage() {
      if ( is_front_page() ) {
        return 'front-page';
      } else if ( is_single() ) {
        return 'single';
      } else if ( is_page() ) {
        return 'page';
      } else if ( is_category() ) {
        return 'category';
      } else if ( is_archive() && ! is_category() ) {
        return 'archive';
      } else {
        return 'undef';
      }
    }

    public function dump() {
    	global $wp_version;
    	
    	echo "wp version: " . $wp_version . "\n";
    	echo "dw version: " . DW_VERSION . "\n";
    	echo "php version: " . phpversion() . "\n";
    	
    	echo "\n";
      echo "list: \n";
      print_r($this->dynwid_list);

      echo "wp_registered_widgets: \n";
      print_r($this->registered_widgets);

      echo "options: \n";
      print_r( $this->getOptions('%', NULL) );
    }

    public function getName($id, $type = 'W') {
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

    public function getOptions($widget_id, $maintype) {
      $opt = array();

      $query = "SELECT widget_id, maintype, name, value FROM " . $this->dbtable . "
                  WHERE widget_id LIKE '" . $widget_id . "' AND maintype LIKE '" . $maintype . "%' ORDER BY name";
      $results = $this->wpdb->get_results($query);

      foreach ( $results as $myrow ) {
        $opt[ ] = array('widget_id' => $myrow->widget_id, 'maintype' => $myrow->maintype, 'name' => $myrow->name, 'value' => $myrow->value);
      }

      return $opt;
    }

    public function hasOptions($widget_id) {
      $query = 'SELECT COUNT(1) AS total FROM ' . $this->dbtable . ' WHERE widget_id = \'' . $widget_id . '\'';
      $count = $this->wpdb->get_var($this->wpdb->prepare($query));

      if ( $count > 0 ) {
        return TRUE;
      } else {
        return FALSE;
      }
    }

    public function message($text) {
      if ( $this->firstmessage ) {
        echo "\n";
        $this->firstmessage = FALSE;
      }
      echo '<!-- ' . $text . ' //-->';
      echo "\n";
    }

    public function resetOptions($widget_id) {
      $query = "DELETE FROM " . $this->dbtable . " WHERE widget_id = '" . $widget_id . "'";
      $this->wpdb->query($query);
    }
  }
?>