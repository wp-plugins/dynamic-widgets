<?php

/**
 *	QTranslate plugin
 * 	http://www.qianqin.de/qtranslate/
 *
 * @version $Id$
 * @copyright 2011 Jacco Drabbe
 */

	function dwGetQTLanguage($lang) {
		global $q_config;
		return $q_config['language_name'][$lang];
	}
?>