<?php
/**
 * WordPress Shopping Cart / WordPress E-Commerce Plugin
 * Only for WPSC < 3.8
 * http://getshopped.org/
 *
 * @version $Id$
 * @copyright 2011 Jacco Drabbe
 */

	function dw_wpsc_get_categories() {
		$wpdb = &$GLOBALS['wpdb'];

		$categories = array();
		$table = WPSC_TABLE_PRODUCT_CATEGORIES;
		$fields = array('id', 'name');
		$query = "SELECT " . implode(', ', $fields) . " FROM " . $table . " WHERE active = '1' ORDER BY name";
		$results = $wpdb->get_results($query);

		foreach ( $results as $myrow ) {
			$categories[$myrow->id] = $myrow->name;
		}

		return $categories;
	}

	function is_dw_wpsc_category($id) {
		$wpsc_query = &$GLOBALS['wpsc_query'];
		$category = $wpsc_query->category;

		if ( is_int($id) ) {
			$id = array($id);
		}

		if ( in_array($category, $id) ) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
?>