<?php

/**
 * dynwid_init_worker.php
 *
 * @version $Id$
 */

	$DW->message('Dynamic Widgets INIT');
	echo "\n" . '<!-- Dynamic Widgets v' . DW_VERSION . ' //-->' . "\n";

	// UserAgent detection
	$DW->message('UserAgent: ' . $DW->useragent);

	// WPML Plugin Support
	if ( defined('ICL_PLUGIN_PATH') ) {
		$wpml_api = ICL_PLUGIN_PATH . DW_WPML_API;

		if ( file_exists($wpml_api) ) {
			require_once($wpml_api);

			$wpmlang = wpml_get_default_language();
			$curlang = wpml_get_current_language();
			$wpml = TRUE;
			$DW->message('WPML language: ' . $curlang);

			if ( $wpmlang != $curlang ) {
				$DW->wpml = TRUE;
				$DW->message('WPML enabled');
				require_once(DW_PLUGIN . 'wpml.php');
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

	if ( $DW->whereami == 'page' ) {
		// WPSC/WPEC Plugin Support
		if ( defined('WPSC_TABLE_PRODUCT_CATEGORIES') ) {
			$wpsc_query = &$GLOBALS['wpsc_query'];

			if ( $wpsc_query->category > 0 ) {
				$DW->wpsc = TRUE;
				$DW->whereami = 'wpsc';
				$DW->message('WPSC detected, page changed to ' . $DW->whereami . ', category: ' . $wpsc_query->category);

				require_once(DW_PLUGIN . 'wpsc.php');
			}
		}
	}

	$DW->dwList($DW->whereami);

?>