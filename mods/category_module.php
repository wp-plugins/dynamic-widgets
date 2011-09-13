<?php
/**
 * Category Module
 *
 * @version $Id$
 * @copyright 2011 Jacco Drabbe
 */

	class DW_Category extends DWModule {
		private static $except = 'Except the categories';
		public static $option = array( 'category' => 'Category Pages' );
		protected static $question = 'Show widget default on category pages?';
		protected static $type = 'complex';
		protected static $wpml = TRUE;

		public static function admin() {
			$DW = $GLOBALS['DW'];

			parent::admin();

			$list = get_categories( array('hide_empty' => FALSE) );
			$catmap = self::getCatChilds(array(), 0, array());
			self::$opt = $DW->getDWOpt($_GET['id'], self::$name);

			self::GUIHeader(self::$option[self::$name], self::$question, FALSE);
			self::GUIOption();

			// Needs an own complex list
			if ( count($list) > DW_LIST_LIMIT ) {
				$select_style = DW_LIST_STYLE;
			}

			echo '<br />' . "\n";
			_e(self::$except, DW_L10N_DOMAIN);
			echo '<br />';
			echo '<div id="' . self::$name . '-select" class="condition-select" ' . ( (isset($select_style)) ? $select_style : '' ) . ' />';
			self::prtCat($catmap, self::$opt->act);
			echo '</div>' . "\n";

			self::GUIFooter();
		}

		public static function getCatChilds($arr, $id, $i) {
			$cat = get_categories( array('hide_empty' => FALSE, 'child_of' => $id) );
			foreach ($cat as $c ) {
				if (! in_array($c->cat_ID, $i) && $c->category_parent == $id ) {
					$i[ ] = $c->cat_ID;
					$arr[$c->cat_ID] = array();
					$a = &$arr[$c->cat_ID];
					$a = self::getCatChilds($a, $c->cat_ID, &$i);
				}
			}
			return $arr;
		}

		public static function prtCat($categories, $category_act, $single = FALSE) {
			$DW = &$GLOBALS['DW'];

			foreach ( $categories as $pid => $childs ) {
				$run = TRUE;

				if ( $DW->wpml ) {
					include_once(DW_MODULES . 'wpml_module.php');
					$wpml_id = DW_WPML::getID($pid, 'tax_category');
					if ( $wpml_id > 0 && $wpml_id <> $pid ) {
						$run = FALSE;
					}
				}

				if ( $run ) {
					$cat = get_category($pid);
					echo '<div style="position:relative;left:15px;">';
					echo '<input type="checkbox" id="' . ( $single ? 'single_' : '' ) . 'category_act_' . $cat->cat_ID . '" name="' . ( $single ? 'single_' : '' ) . 'category_act[]" value="' . $cat->cat_ID . '" ' . ( isset($category_act) && count($category_act) > 0 && in_array($cat->cat_ID, $category_act) ? 'checked="checked"' : '' ) . ( $single ? ' onclick="ci(\'single_category_act_' . $cat->cat_ID . '\')"' : '' ) . ' /> <label for="' . ( $single ? 'single_' : '' ) . 'category_act_' . $cat->cat_ID . '">' . $cat->name . '</label><br />';

					if ( count($childs) > 0 ) {
						self::prtCat($childs, $category_act, $single);
					}
					echo '</div>';
				}
			}
		}
	}
?>

