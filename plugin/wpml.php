<?php
/**
 * WordPress MultiLingual Plugin
 * http://wpml.org/
 *
 *
 * @version $Id$
 * @copyright 2010
 */

	function dw_wpml_get_id($content_id, $content_type = 'post_page') {
		$language_code = wpml_get_default_language();
		$lang = wpml_get_content_translation($content_type, $content_id, $language_code);

		if ( is_array($lang) ) {
			$id = $lang[$language_code];
		} else {
			$id = 0;
		}

		return $id;
	}
?>