<?php

/**
 * dynwid_init_worker.php
 *
 * @version $Id$
 * @copyright 2010 Jacco Drabbe
 */

	$DW->message('Dynamic Widgets INIT');

	// WPML Plugin Support
	if ( defined('ICL_PLUGIN_PATH') ) {
		$wpml_api = ICL_PLUGIN_PATH . DW_WPML_API;

		if ( file_exists($wpml_api) ) {
			require_once($wpml_api);

			$wpmlang = wpml_get_default_language();
			$curlang = wpml_get_current_language();

			if ( $wpmlang != $curlang ) {
				$DW->wpml = TRUE;
				$DW->message('WPML enabled, default language: ' . $wpmlang);
			}
		}
	}

	$DW->message('User has role(s): ' . implode(', ', $DW->userrole));

	$custom_post_type = FALSE;
	$DW->whereami = $DW->detectPage();
	$DW->message('Page is ' . $DW->whereami);
	if ( $DW->whereami == 'single' ) {
		$post = $GLOBALS['post'];
		$DW->message('post_id = ' . $post->ID);

		/* WordPress 3.0 and higher: Custom Post Types */
		if ( version_compare($GLOBALS['wp_version'], '3.0', '>=') ) {
			$post_type = get_post_type($post);
			$DW->message('Post Type = ' . $post_type);
			if ( $post_type != 'post' ) {
				$DW->custom_post_type = TRUE;
				$DW->whereami = $post_type;
				$DW->message('Custom Post Type detected, page changed to ' . $DW->whereami);
			}
		}
	}
	$DW->dwList($DW->whereami);

?>