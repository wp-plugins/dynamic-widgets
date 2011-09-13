<?php
/**
 * dynwid_class.php - Dynamic Widgets Classes (PHP5)
 *
 * @version $Id$
 * @copyright 2011 Jacco Drabbe
 */

	class DWMessageBox {
		private static $leadtext;
		private static $message;
		public static  $type;

		public static function create($lead, $msg) {
			self::setlead($lead);
			self::setMessage($msg);
			self::output();
		}

		public static function output() {
			switch ( self::$type ) {
				case 'error':
					$class = 'error';
					break;
				default:
					$class = 'updated fade';
			}

			echo '<div class="' . $class . '" id="message">';
			echo '<p>';
			if (! empty(self::$leadtext) ) {
				echo '<strong>' . self::$leadtext . '</strong> ';
			}
			echo self::$message;
			echo '</p>';
			echo '</div>';
		}

		public static function setLead($text) {
			self::$leadtext = $text;
		}

		public static function setMessage($text) {
			self::$message = $text;
		}

		public static function setTypeMsg($type) {
			self::$type = $type;
		}
	}

	class DWOpts {
		public  $act;
		public  $checked = 'checked="checked"';
		public  $count;
		public  $default;
		private $type;

		public function __construct($result, $type) {
			$this->act = array();
			$this->count = count($result);
			$this->type = $type;
			if ( $this->count > 0 ) {
				foreach ( $result as $condition ) {
					if ( $condition->maintype == $this->type ) {
						if ( $condition->name == 'default' || empty($condition->name) ) {
							$this->default = $condition->value;
						} else {
							$this->act[ ] = $condition->name;
						}
					}
				}
			} else {
				$this->default = '1';
			}

			// in some cases the default is (still) null
			if ( is_null($this->default) ) {
				$this->default = '1';
				$this->count = 0;
			}
		}

		public function selectNo() {
			if ( $this->default == '0' ) {
				return TRUE;
			} else {
				return FALSE;
			}
		}

		public function selectYes() {
			if ( $this->default == '1' ) {
				return TRUE;
			} else {
				return FALSE;
			}
		}
	}

	abstract class DWModule {
		protected static $info = FALSE;
		protected static $name;
		public static $option;
		protected static $opt;
		protected static $overrule = FALSE;
		public static $plugin = FALSE;
		protected static $type = 'simple';
		protected static $wpml = FALSE;

	  public static function admin() {
	  	$DW = &$GLOBALS['DW'];

	  	$classname = self::getClassName();
	  	$vars = self::getVars($classname);
	  	self::setName($classname);

	  	// Would be so much easier if we could require PHP > 5.3: $name::
			self::checkOverrule();

	  	if ( $vars['plugin'] !== FALSE ) {
	  		self::registerPlugin($vars['plugin']);
	  	}

	  	if ( $vars['type'] == 'simple' ) {
	  		self::mkGUI($vars['type'], $vars['option'][self::$name], $vars['question'], $vars['info']);
	  	}
	  }

		public static function checkOverrule() {
			$DW = &$GLOBALS['DW'];

			$classname = self::getClassName();
			$vars = self::getVars($classname);
			self::setName($classname);

			if ( isset($vars['overrule']) && $vars['overrule'] && ! in_array(self::$name, $DW->overrule_maintype) ) {
				$DW->overrule_maintype[ ] = self::$name;
			}
		}

		protected static function getClassName() {
			$classname = get_called_class();
			return $classname;
		}

		protected static function getVars($classname) {
			$vars = get_class_vars($classname);
			return $vars;
		}

		public static function GUIComplex($except, $list, $name = NULL) {
			if (! is_null($name) ) {
				self::$name = $name;
			}

			if ( count($list) > DW_LIST_LIMIT ) {
				$select_style = DW_LIST_STYLE;
			}

			if ( count($list) > 0 ) {
				echo '<br />' . "\n";
				_e($except, DW_L10N_DOMAIN);
				echo '<br />';
				echo '<div id="' . self::$name . '-select" class="condition-select" ' . ( (isset($select_style)) ? $select_style : '' ) . ' />';
				foreach ( $list as $key => $value ) {
					echo '<input type="checkbox" id="' . self::$name . '_act_' . $key . '" name="' . self::$name . '_act[]" value="' . $key . '" ' . ( (self::$opt->count > 0 && in_array($key, self::$opt->act)) ? 'checked="checked"' : '' ) . ' /> <label for="' . self::$name . '_act_' . $key . '">' . $value . '</label><br />' . "\n";
				}
				echo '</div>' . "\n";
			}
		}

		public static function GUIFooter() {
			echo '</div><!-- end dynwid_conf -->' . "\n";
		}

		public static function GUIHeader($title, $question, $info, $post_title = NULL, $opt = NULL) {
			$DW = &$GLOBALS['DW'];

			$classname = self::getClassName();
			$vars = self::getVars($classname);
			if ( $vars['wpml'] !== FALSE ) {
				$wpml = TRUE;
			}

			if (! is_null($post_title) ) {
				$title  = __($title, DW_L10N_DOMAIN);
				$title .= ' ' . $post_title;
			}

			if (! is_null($opt) ) {
				self::$opt = $opt;
			}

			echo '<!-- ' . $title . '//-->' . "\n";
			echo '<h4><b>' . __($title, DW_L10N_DOMAIN) . '</b>' . ( (self::$opt->count > 0) ? ' <img src="' . $DW->plugin_url . 'img/checkmark.gif" alt="Checkmark" />' : '' ) . ' ' . ( ($DW->wpml && $wpml) ? DW_WPML::$icon : '' ) . '</h4>' . "\n";
			echo '<div class="dynwid_conf">' . "\n";
			_e($question, DW_L10N_DOMAIN);

			if ( $info !== FALSE ) {
				echo ' <img src="' . $DW->plugin_url . 'img/info.gif" alt="info" title="' . __('Click to toggle info', DW_L10N_DOMAIN) . '" onclick="divToggle(\'' . self::$name . '\')" /><br />' . "\n";
				echo '<div><div id="' . self::$name . '" class="infotext">' . "\n";
				_e($info, DW_L10N_DOMAIN);
				echo '</div></div>' . "\n";
			} else {
				echo '<br />' . "\n";
			}
		}

		public static function GUIOption($name = NULL, $opt = NULL) {
			$DW = &$GLOBALS['DW'];

			if (! is_null($name) ) {
				self::$name = $name;
			}

			if (! is_null($opt) ) {
				self::$opt = $opt;
			}

			$DW->dumpOpt(self::$opt);
			echo '<input type="radio" name="' . self::$name . '" value="yes" id="' . self::$name . '-yes" ' . ( (self::$opt->selectYes()) ? self::$opt->checked : '' ) . ' /> <label for="' . self::$name . '-yes">' . __('Yes') . '</label>' . "\n";
			echo '<input type="radio" name="' . self::$name . '" value="no" id="' . self::$name . '-no" ' . ( (self::$opt->selectNo()) ? self::$opt->checked : '' ) . ' /> <label for="' . self::$name . '-no">' . __('No') . '</label>' . "\n";
		}

		public static function mkGUI($type, $title, $question, $info, $except = FALSE, $list = FALSE, $name = NULL) {
			$DW = &$GLOBALS['DW'];

			if (! is_null($name) ) {
				self::$name = $name;
			}

			self::$opt = $DW->getDWOpt($_GET['id'], self::$name);

			self::GUIHeader($title, $question, $info);
			self::GUIOption();
			if ( $type == 'complex' ) {
				self::GUIComplex($except, $list);
			}
			self::GUIFooter();
		}

		protected static function registerOption($dwoption) {
			$DW = &$GLOBALS['DW'];

			foreach ( $dwoption as $key => $value ) {
				$DW->dwoptions[$key] = __($value, DW_L10N_DOMAIN);
			}
		}

		public static function registerPlugin($plugin) {
			$DW = &$GLOBALS['DW'];

			foreach ( $plugin as $key => $value ) {
				if (! isset($DW->$key) ) {
					$DW->$key = $value;
				}
			}
		}

		public static function save($name, $type = 'simple') {
			$DW = &$GLOBALS['DW'];

			switch ( $type ) {
				case 'complex':
					$act = $name . '_act';

					if ( isset($_POST[$act]) && count($_POST[$act]) > 0 ) {
						$DW->addMultiOption($_POST['widget_id'], $name, $_POST[$name], $_POST[$act]);
					} else if ( isset($_POST[$name]) && $_POST[$name] == 'no' ) {
						$DW->addSingleOption($_POST['widget_id'], $name);
					}
					break;

				// simple
				default:
					if ( isset($_POST[$name]) && $_POST[$name] == 'no' ) {
						$DW->addSingleOption($_POST['widget_id'], $name);
					}
			} // switch
		}

		protected static function setName($classname) {
			self::$name = strtolower(substr($classname, 3));	// Chop off the "DW_"
			self::$name = str_replace('_', '-', self::$name);
		}
	}

  class dynWid {
    private $dbtable;
  	public  $dwoptions = array();
    public  $dynwid_list;
  	public  $enabled;
    private $firstmessage = TRUE;
  	public  $listmade = FALSE;
		public  $overrule_maintype = array();
    private $registered_sidebars;
    public  $registered_widget_controls;
    public  $registered_widgets;
  	public  $removelist = array();
    public  $sidebars;
  	public  $template;
    public  $plugin_url;
  	public  $useragent;
    public  $userrole;
  	public  $whereami;
    private $wpdb;

    public function __construct() {
      if ( is_user_logged_in() ) {
				$this->userrole = $GLOBALS['current_user']->roles;
      } else {
        $this->userrole = array('anonymous');
      }

      $this->registered_sidebars = $GLOBALS['wp_registered_sidebars'];
      $this->registered_widget_controls = &$GLOBALS['wp_registered_widget_controls'];
      $this->registered_widgets = &$GLOBALS['wp_registered_widgets'];
      $this->sidebars = wp_get_sidebars_widgets();
    	$this->useragent = $this->getBrowser();
      $this->plugin_url = WP_PLUGIN_URL . '/' . str_replace( basename(__FILE__), '', plugin_basename(__FILE__) );

    	// DB init
      $this->wpdb = $GLOBALS['wpdb'];
      $this->dbtable = $this->wpdb->prefix . DW_DB_TABLE;
    	$query = "SHOW TABLES LIKE '" . $this->dbtable . "'";
    	$result = $this->wpdb->get_var($query);

    	if ( is_null($result) ) {
    		$this->enabled = FALSE;
    	} else {
    		$this->enabled = TRUE;
    	}
    }

  	public function __get($name) {
  		return $this->$name;
  	}

  	public function __isset($name) {
  		if ( isset($this->$name) ) {
  			return TRUE;
  		}
  		return FALSE;
  	}

  	public function __set($name, $value) {
  		$this->$name = $value;
  	}

    public function addChilds($widget_id, $maintype, $default, $act, $childs) {
  		$child_act = array();
			foreach ( $childs as $opt ) {
  			if ( in_array($opt, $act) ) {
  				$childs_act[ ] = $opt;
  			}
  		}
  		$this->addMultiOption($widget_id, $maintype, $default, $childs_act);
  	}

    public function addDate($widget_id, $dates) {
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

    public function addMultiOption($widget_id, $maintype, $default, $act) {
    	$insert = TRUE;

      if ( $default == 'no' ) {
        $opt_default = '0';
        $opt_act = '1';
      } else {
        $opt_default = '1';
        $opt_act = '0';
      }

    	// Check single-post or single-option coming from post or tag screen
    	if ( $maintype == 'single-post' || $maintype == 'single-tag' ) {
    		$query = "SELECT COUNT(1) AS total FROM " . $this->dbtable . " WHERE widget_id = '" . $widget_id . "' AND maintype = '" . $maintype . "' AND name = 'default'";
    		$count = $this->wpdb->get_var($this->wpdb->prepare($query));
    		if ( $count > 0 ) {
    			$insert = FALSE;
    		}
    	}

    	if ( $insert ) {
    		$query = "INSERT INTO " . $this->dbtable . "
                      (widget_id, maintype, name, value)
                    VALUES
                      ('" . $widget_id . "', '" . $maintype . "', 'default', '" . $opt_default . "')";
    		$this->wpdb->query($query);
    	}
      foreach ( $act as $option ) {
        $query = "INSERT INTO " . $this->dbtable . "
                      (widget_id, maintype, name, value)
                    VALUES
                      ('" . $widget_id . "', '" . $maintype . "', '" . $option . "', '" . $opt_act . "')";
        $this->wpdb->query($query);
      }
    }

    public function addSingleOption($widget_id, $maintype, $value = '0') {
      $query = "INSERT INTO " . $this->dbtable . "
                    (widget_id, maintype, value)
                  VALUES
                    ('" . $widget_id . "', '" . $maintype . "', '" . $value . "')";
      $this->wpdb->query($query);
    }

    public function checkWPhead() {
      $ct = current_theme_info();
      $headerfile = $ct->template_dir . '/header.php';
      if ( file_exists($headerfile) ) {
        $buffer = file_get_contents($headerfile);
        if ( strpos($buffer, 'wp_head()') ) {
          // wp_head() found
          return 1;
        } else {
          // wp_head() not found
          return 0;
        }
      } else {
        // wp_head() unable to determine
        return 2;
      }
    }

    private function createList() {
      $this->dynwid_list = array();

      foreach ( $this->sidebars as $sidebar_id => $widgets ) {
        if ( count($widgets) > 0 ) {
          foreach ( $widgets as $widget_id ) {
            if ( $this->hasOptions($widget_id) ) {
              $this->dynwid_list[ ] = $widget_id;
            }
          } // END foreach widgets
        }
      } // END foreach sidebars
    }

    public function deleteOption($widget_id, $maintype, $name = '') {
      $query = "DELETE FROM " . $this->dbtable . " WHERE widget_id = '" . $widget_id . "' AND maintype = '" . $maintype ."'";
      if (! empty($name) ) {
      	$query .= " AND name = '" . $name . "'";
      }
      $this->wpdb->query($query);
    }

    public function detectPage() {
    	if ( is_front_page() && get_option('show_on_front') == 'posts' ) {
        return 'front-page';
      } else if ( is_home() && get_option('show_on_front') == 'page' ) {
      	return 'home';
      } else if ( is_attachment() ) {
      	return 'attachment';					// must be before is_single(), otherwise detects as 'single'
      } else if ( is_single() ) {
        return 'single';
      } else if ( is_page() ) {
        return 'page';
      } else if ( is_author() ) {
        return 'author';
      } else if ( is_category() ) {
        return 'category';
      } else if ( function_exists('is_post_type_archive') && is_post_type_archive() ) {
    		return 'cp_archive';				// must be before is_archive(), otherwise detects as 'archive' in WP 3.1.0
      } else if ( function_exists('is_tax') && is_tax() ) {
      	return 'tax_archive';
      } else if ( is_archive() && ! is_category() && ! is_author() ) {
        return 'archive';
      } else if ( is_404() ) {
      	return 'e404';
      } else if ( is_search() ) {
        return 'search';
      } else if ( function_exists('is_pod_page') && is_pod_page() ) {
      	return 'pods';
      } else {
        return 'undef';
      }
    }

    public function dump() {
    	echo "wp version: " . $GLOBALS['wp_version'] . "\n";
      echo "wp_head: " . $this->checkWPhead() . "\n";
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
      $this->createList();
      foreach ( $this->dynwid_list as $widget_id ) {
        $list[$widget_id] = strip_tags($this->getName($widget_id));
      }
      print_r($list);

      echo "wp_registered_widgets: \n";
      print_r($this->registered_widgets);

      echo "options: \n";
      print_r( $this->getOpt('%', NULL) );

      echo "\n";
      echo serialize($this->getOpt('%', NULL));
    }

    public function dumpOpt($opt) {
      if ( DW_DEBUG && count($opt) > 0 ) {
        var_dump($opt);
      }
    }

    // replacement for createList() to make the worker faster
    public function dwList($whereami) {
      $this->dynwid_list = array();
      if ( $whereami == 'home' ) {
        $whereami = 'page';
      }

      $query = "SELECT DISTINCT widget_id FROM " . $this->dbtable . "
                  WHERE  maintype LIKE '" . $whereami . "%'";

      if ( count($this->overrule_maintype) > 0 ) {
      	$query .= " OR maintype IN ";
      	$q = array();
      	foreach ( $this->overrule_maintype as $omt ) {
      		$q[ ] = "'" . $omt . "'";
      	}
      	$query .= "(" . implode(', ', $q) . ")";
      }

      $results = $this->wpdb->get_results($query);
      foreach ( $results as $myrow ) {
        $this->dynwid_list[ ] = $myrow->widget_id;
      }
    }

  	private function getBrowser() {
  		global $is_gecko, $is_IE, $is_opera, $is_NS4, $is_safari, $is_chrome;

  		if ( $is_gecko ) {
  			return 'gecko';
  		} else if ( $is_IE ) {
  			if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 6.') !== FALSE ) {
  				return 'msie6';
  			} else {
  				return 'msie';
  			}
  		} else if ( $is_opera ) {
  			return 'opera';
  		} else if ( $is_NS4 ) {
  			return 'ns';
  		} else if ( $is_safari ) {
  			return 'safari';
  		} else if ( $is_chrome ) {
  			return 'chrome';
  		} else {
  			return 'undef';
  		}
  	}

  	public function getDWOpt($widget_id, $maintype) {
  		if ( $maintype == 'home' ) {
  			$maintype = 'page';
  		}
  		$query = "SELECT widget_id, maintype, name, value FROM " . $this->dbtable . "
                 WHERE widget_id LIKE '" . $widget_id . "'
                   AND maintype LIKE '" . $maintype . "%'
                 ORDER BY maintype, name";
  		$results = new DWOpts($this->wpdb->get_results($query), $maintype);
  		return $results;
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

      if ( $type == 'W' && isset($lookup[$id]['params'][0]['number']) ) {
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

    public function getOpt($widget_id, $maintype, $admin = TRUE) {
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
                    AND (maintype LIKE '" . $maintype . "%'";

      	if ( count($this->overrule_maintype) > 0 ) {
      		$query .= " OR maintype IN (";
      		$q = array();
      		foreach ( $this->overrule_maintype as $omt ) {
      			$q[ ] = "'" . $omt . "'";
      		}
      		$query .= implode(', ', $q);
      	}


        $query .= ")) ORDER BY maintype, name";
      }
      $this->message('Q: ' . $query);

			$results = $this->wpdb->get_results($query);
      return $results;
    }

  	public function getParents($type, $arr, $id) {
  		if ( $type == 'page' ) {
  			$obj = get_page($id);
  		} else {
  			$obj = get_post($id);
  		}

  		if ( $obj->post_parent > 0 ) {
  			$arr[ ] = $obj->post_parent;
  			$a = &$arr;
  			$a = $this->getParents($type, $a, $obj->post_parent);
  		}

  		return $arr;
  	}

  	public function getTaxParents($tax_name, $arr, $id) {
  		$obj = get_term_by('id', $id, $tax_name);
  		if ( $obj->parent > 0 ) {
  			$arr[ ] = $obj->parent;
  			$a = &$arr;
  			$a = $this->getTaxParents($tax_name, $a, $obj->parent);
  		}
  		return $arr;
  	}

    public function hasOptions($widget_id) {
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

  	public function housekeeping() {
  		$widgets = array_keys($this->registered_widgets);

  		$query = "SELECT DISTINCT widget_id FROM " . $this->dbtable;
  		$results = $this->wpdb->get_results($query);
  		foreach ( $results as $myrow ) {
  			if (! in_array($myrow->widget_id, $widgets) ) {
  				$this->resetOptions($myrow->widget_id);
  			}
  		}
  	}

  	public function loadModules() {
  		$dh = opendir(DW_MODULES);
  		while ( ($file = readdir($dh)) !== FALSE) {
  			if ( $file != '.' && $file != '..' && substr(strrchr($file, '_'), 1) == 'module.php' ) {
  				include_once(DW_MODULES . $file);
  			}
  		}
  	}

    public function message($text) {
      if ( DW_DEBUG ) {
        if ( $this->firstmessage ) {
          echo "\n";
          $this->firstmessage = FALSE;
        }
        echo '<!-- ' . $text . ' //-->';
        echo "\n";
      }
    }

    public function resetOptions($widget_id) {
      $query = "DELETE FROM " . $this->dbtable . " WHERE widget_id = '" . $widget_id . "'";
      $this->wpdb->query($query);
    }
  }
?>
