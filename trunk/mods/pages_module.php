<?php
/**
 * Pages Module
 *
 * @version $Id$
 * @copyright 2011 Jacco Drabbe
 */

	class DW_Page extends DWModule {
		protected static $info;
		public static $option = array( 'page' => 'Pages' );
		public static $opt_page_childs;
		public static $opt_page;
		protected static $question = 'Show widget default on static pages?';
		public static $static_page;
		protected static $type = 'custom';
		protected static $wpml = TRUE;

		public static function admin() {
			$DW = &$GLOBALS['DW'];

			parent::admin();

			self::$opt = $DW->getDWOpt($_GET['id'], 'page');
			self::$opt_page = self::$opt;
			if ( self::$opt->count > 0 ) {
				self::$opt_page_childs = $DW->getDWOpt($_GET['id'], 'page-childs');
			}

			$pages = get_pages();
			$num_pages = count($pages);
			unset($pages);

			if ( $num_pages < DW_PAGE_LIMIT ) {
				$hierarchy = TRUE;
			} else {
				$hierarchy = FALSE;
			}

			// For childs we double the number of pages because of addition of 'All childs' option
			if ( ($hierarchy && ($num_pages * 2 > DW_LIST_LIMIT)) || ($num_pages  > DW_LIST_LIMIT) ) {
				$page_condition_select_style = DW_LIST_STYLE;
			}

			self::$static_page = array();
			if ( get_option('show_on_front') == 'page' ) {
				$id = get_option('page_on_front');
				self::$static_page[$id] = __('Front page', DW_L10N_DOMAIN);
				if ( get_option('page_on_front') == get_option('page_for_posts') ) {
					self::$static_page[$id] .= ', ' . __('Posts page', DW_L10N_DOMAIN);
				}
			}

			if ( $num_pages < DW_PAGE_LIMIT ) {
				$childs_infotext = self::infoText();
			} else {
				$childs_infotext = __('Unfortunately the childs-function has been disabled
						because you have more than the limit of pages.', DW_L10N_DOMAIN) . '(' . DW_PAGE_LIMIT . ')';
			}
			self::$info = $childs_infotext;
			self::GUIHeader(self::$option[self::$name], self::$question, self::$info);
			self::GUIOption();

			if ( $num_pages > 0 ) {
				$DW->dumpOpt(self::$opt_page_childs);

				echo '<br />';
				_e('Except the page(s)', DW_L10N_DOMAIN);
				echo '<br />';
				echo '<div id="page-select" class="condition-select" ' . ( (isset($page_condition_select_style)) ? $page_condition_select_style : '' ) . ' />';

				if ( $num_pages < DW_PAGE_LIMIT ) {
					wp_list_pages( array('title_li' => '', 'walker' => new DW_Page_Walker()) );
				} else {
					wp_list_pages( array('title_li' => '', 'depth' => -1, 'walker' => new DW_Page_Walker()) );
				}

				echo '</div>';
			}

			self::GUIFooter();
		}

		public static function infoText() {
			return __('Checking the "All childs" option, makes the exception rule apply
						to the parent and all items under it in all levels. Also future items
						under the parent. It\'s not possible to apply an exception rule to
						"All childs" without the parent.', DW_L10N_DOMAIN);
		}

	}

	class DW_Page_Walker extends Walker_Page {
		private $post_page;

		function __construct() {
			$this->post_page = get_option('page_for_posts');
		}

		function start_lvl(&$output, $depth) {
			$indent = str_repeat("\t", $depth);
			$output .= "\n" . $indent . '<div style="position:relative;left:15px;width:95%;">' . "\n";
		}

		function end_lvl(&$output, $depth) {
			$indent = str_repeat("\t", $depth);
			$output .= $indent . '</div>' . "\n";
		}

		function start_el(&$output, $page, $depth, $args, $current_page) {
			extract($args, EXTR_SKIP);

			if ( $depth ) {
				$indent = str_repeat("\t", $depth);
			} else {
				$indent = '';
			}

			if ( $page->ID <> $this->post_page ) {
				$output .= $indent . '<input type="checkbox" id="page_act_' . $page->ID . '" name="page_act[]" value="' . $page->ID . '"  ' . ( isset(DW_Page::$opt_page->act) && count(DW_Page::$opt_page->act) > 0 && in_array($page->ID, DW_Page::$opt_page->act) ? 'checked="checked"' : '' ) . ' onchange="chkChild(\'page\', ' . $page->ID . ')" /> <label for="page_act_' . $page->ID . '">' . apply_filters( 'the_title', $page->post_title, $page->ID ) . ' ' . ( get_option('show_on_front') == 'page' && isset(DW_Page::$static_page[$page->ID]) ? '(' . DW_Page::$static_page[$page->ID] . ')' : '' ) . '</label>';
				$output .= '<br />';

				if ( $args['depth'] > -1 ) {
					$output .= '<div style="position:relative;left:15px;width:95%;">';
					$output .= '<input type="checkbox" id="page_childs_act_' . $page->ID . '" name="page_childs_act[]" value="' . $page->ID . '" ' . ( isset(DW_Page::$opt_page_childs->act) && count(DW_Page::$opt_page_childs->act) > 0 && in_array($page->ID, DW_Page::$opt_page_childs->act) ? 'checked="checked"' : '' ) . ' onchange="chkParent(\'page\', ' . $page->ID . ')" /> <label for="page_childs_act_' . $page->ID . '"><em>' . __('All childs', DW_L10N_DOMAIN) . '</em></div>';
				}

			}
		}

		function end_el(&$output, $page, $depth) {
			// Just an empty function, making sure parent::end_el() does not fire
			return;
		}
	}