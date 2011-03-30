<?php
/**
 * Category Module
 *
 * @version $Id$
 * @copyright 2011 Jacco Drabbe
 */

	function getCatChilds($arr, $id, $i) {
		$cat = get_categories( array('hide_empty' => FALSE, 'child_of' => $id) );
		foreach ($cat as $c ) {
			if (! in_array($c->cat_ID, $i) ) {
				$i[ ] = $c->cat_ID;
				$arr[$c->cat_ID] = array();
				$a = &$arr[$c->cat_ID];
				$a = getCatChilds($a, $c->cat_ID, &$i);
			}
		}
		return $arr;
	}

	function prtCat($categories, $category_act) {
		foreach ( $categories as $pid => $childs ) {
			$cat = get_category($pid);

			echo '<div style="position:relative;left:15px;">';
			echo '<input type="checkbox" id="cat_act_' . $cat->cat_ID . '" name="category_act[]" value="' . $cat->cat_ID . '" ' . ( isset($category_act) && count($category_act) > 0 && in_array($cat->cat_ID, $category_act) ? 'checked="checked"' : '' ) . ' /> <label for="cat_act_' . $cat->cat_ID . '">' . $cat->name . '</label><br />';

			if ( count($childs) > 0 ) {
				prtCat($childs, $category_act);
			}
			echo '</div>';
		}
	}

	$category_yes_selected = 'checked="checked"';
	$opt_category = $DW->getOptions($_GET['id'], 'category');
	if ( count($opt_category) > 0 ) {
		$category_act = array();
		foreach ( $opt_category as $category_condition ) {
			if ( $category_condition['name'] == 'default' || empty($category_condition['name']) ) {
				$category_default = $category_condition['value'];
			} else {
				$category_act[ ] = $category_condition['name'];
			}
		}

		if ( $category_default == '0' ) {
			$category_no_selected = $category_yes_selected;
			unset($category_yes_selected);
		}
	}

	$category = get_categories( array('hide_empty' => FALSE) );
	if ( count($category) > DW_LIST_LIMIT ) {
		$category_condition_select_style = DW_LIST_STYLE;
	}

	$catmap = getCatChilds(array(), 0, array());
?>

<h4><b><?php _e('Category Pages', DW_L10N_DOMAIN); ?></b> <?php echo ( count($opt_category) > 0 ? ' <img src="' . $DW->plugin_url . 'img/checkmark.gif" alt="Checkmark" />' : '' ) . ( $DW->wpml ? $wpml_icon : '' ); ?></h4>
<div class="dynwid_conf">
<?php _e('Show widget default on category pages?', DW_L10N_DOMAIN); ?><br />
<?php $DW->dumpOpt($opt_category); ?>
<input type="radio" name="category" value="yes" id="category-yes" <?php echo ( isset($category_yes_selected) ? $category_yes_selected : '' ); ?> /> <label for="category-yes"><?php _e('Yes'); ?></label>
<input type="radio" name="category" value="no" id="category-no" <?php echo ( isset($category_no_selected) ? $category_no_selected : '' ); ?> /> <label for="category-no"><?php _e('No'); ?></label><br />
<?php _e('Except the categories', DW_L10N_DOMAIN); ?>:<br />
<div id="category-select" class="condition-select" <?php echo ( isset($category_condition_select_style) ? $category_condition_select_style : '' ); ?>>
<?php prtCat($catmap, $category_act); ?>
</div>
</div><!-- end dynwid_conf -->
