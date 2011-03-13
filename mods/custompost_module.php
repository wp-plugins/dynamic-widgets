<?php

/**
 * Custom Post Type Module
 *
 * @version $Id$
 * @copyright 2011 Jacco Drabbe
 */

	/* WordPress 3.0 and higher: Custom Post Types */
	if ( version_compare($GLOBALS['wp_version'], '3.0', '>=') ) {
		if ( $DW->wpml ) {
			require(DW_PLUGIN . 'wpml.php');
		}
		
		function getCPostChilds($type, $arr, $id, $i) {
			$post = get_posts('post_type=' . $type . '&post_parent=' . $id);

			foreach ($post as $p ) {
				if (! in_array($p->ID, $i) ) {
					$i[ ] = $p->ID;
					$arr[$p->ID] = array();
					$a = &$arr[$p->ID];
					$a = getCPostChilds($type, $a, $p->ID, &$i);
				}
			}
			return $arr;
		}

		function prtCPost($type, $ctid, $posts, $posts_act, $posts_childs_act) {
			$DW = $GLOBALS['DW'];		
			
			foreach ( $posts as $pid => $childs ) {
				$run = TRUE;
				$post = get_post($pid);
				
				if ( $DW->wpml ) {
					$wpml_id = dw_wpml_get_id($pid, $content_type = 'post_' . $type);
					if ( $wpml_id > 0 && $wpml_id <> $pid ) {
						$run = FALSE;
					}
				}

				if ( $run ) {
					echo '<div style="position:relative;left:15px;">';
					echo '<input type="checkbox" id="' . $type . '_act_' . $post->ID . '" name="' . $type . '_act[]" value="' . $post->ID . '" ' . ( isset($posts_act) && count($posts_act) > 0 && in_array($post->ID, $posts_act) ? 'checked="checked"' : '' ) . ' onchange="chkCPChild(\'' . $type . '\',' . $pid . ')" /> <label for="' . $type . '_act_' . $post->ID . '">' . $post->post_title . '</label><br />';

					if ( $ctid->hierarchical ) {
						echo '<div style="position:relative;left:15px;">';
						echo '<input type="checkbox" id="' . $type . '_child_' . $pid . '" name="' . $type . '_childs_act[]" value="' . $pid . '" ' . ( isset($posts_childs_act) && count($posts_childs_act) > 0 && in_array($pid, $posts_childs_act) ? 'checked="checked"' : '' ) . ' onchange="chkCPParent(\'' . $type . '\',' . $pid . ')" /> <label for="' . $type . '_child_' . $pid . '"><em>' . __('All childs', DW_L10N_DOMAIN) . '</em></label><br />';
						echo '</div>';
					}

					if ( count($childs) > 0 ) {
						prtCPost($type, $ctid, $childs, $posts_act, $posts_childs_act);
					}
					echo '</div>';
				}
			}
		}

		$args = array(
		'public'   => TRUE,
		'_builtin' => FALSE
	);
		$post_types = get_post_types($args, 'objects', 'and');

		foreach ( $post_types as $type => $ctid ) {
			// Prepare
			$custom_yes_selected = 'checked="checked"';
			$opt_custom = $DW->getOptions($_GET['id'], $type);
			if ( count($opt_custom) > 0 ) {
				$custom_act = array();
				$custom_childs_act = array();

				foreach ( $opt_custom as $custom_condition ) {
					if ( $custom_condition['maintype'] == $type ) {
						if ( $custom_condition['name'] == 'default' || empty($custom_condition['name']) ) {
							$custom_default = $custom_condition['value'];
						} else {
							$custom_act[ ] = $custom_condition['name'];
						}
					}
				}

				if ( $custom_default == '0' ) {
					$custom_no_selected = $custom_yes_selected;
					unset($custom_yes_selected);
				}

				// -- Childs
				if ( $ctid->hierarchical ) {
					$opt_custom_childs = $DW->getOptions($_GET['id'], $type . '-childs');
					if ( count($opt_custom_childs) > 0 ) {
						foreach ( $opt_custom_childs as $child_condition ) {
							if ( $child_condition['name'] != 'default' ) {
								$custom_childs_act[ ] = $child_condition['name'];
							}
						}
					}
				}
			}

			$loop = new WP_Query( array('post_type' => $type) );
			if ( $loop->post_count > DW_LIST_LIMIT ) {
				$custom_condition_select_style = DW_LIST_STYLE;
			}

			$cpmap = getCPostChilds($type, array(), 0, array());
			$tax_list = get_object_taxonomies($type, 'objects');

			// Output
			echo '<h4><b>' . __('Custom Post Type', DW_L10N_DOMAIN) . ' <em>' . $ctid->label . '</em></b> ' . ( count($opt_custom) > 0 ? ' <img src="' . $DW->plugin_url . 'img/checkmark.gif" alt="Checkmark" />' : '' ) . ( $DW->wpml ? $wpml_icon : '' ) . '</h4>';
			echo '<div class="dynwid_conf">';
			echo __('Show widget on', DW_L10N_DOMAIN) . ' ' . $ctid->label . '? ' . ( $ctid->hierarchical || count($tax_list) > 0 ? '<img src="' . $DW->plugin_url . 'img/info.gif" alt="info" onclick="divToggle(\'custom_' . $type . '\');" />' : '' ) . '<br />';
			echo '<input type="hidden" name="post_types[]" value="' . $type . '" />';
			$DW->dumpOpt($opt_custom);

			echo '<div>';
			echo '<div id="custom_' . $type . '" class="infotext">';
			echo ( $ctid->hierarchical ? '<p>' . $childs_infotext . '</p>' : '' );
			echo ( count($tax_list) > 0 ? '<p>All exceptions (Titles and Taxonomies) work in a logical OR condition. That means when one of the exceptions is met, the exception rule is applied.</p>' : '' );
			echo '</div>';
			echo '</div>';

			echo '<input type="radio" name="' . $type . '" value="yes" id="' . $type . '-yes" ' . ( isset($custom_yes_selected) ? $custom_yes_selected : '' ) . ' /> <label for="' . $type . '-yes">' . __('Yes') . '</label> ';
			echo '<input type="radio" name="' . $type . '" value="no" id="' . $type . '-no" ' . ( isset($custom_no_selected) ? $custom_no_selected : '' ) . ' /> <label for="' . $type . '-no">' . __('No') . '</label><br />';

			echo __('Except for', DW_L10N_DOMAIN) . ':<br />';
			echo '<div id="' . $type . '-select" class="condition-select" ' . ( isset($custom_condition_select_style) ? $custom_condition_select_style : '' ) . '>';

			echo '<div style="position:relative;left:-15px">';
			prtCPost($type, $ctid, $cpmap, $custom_act, $custom_childs_act);
			echo '</div>';
			echo '</div>';

			foreach ( $tax_list as $tax_type ) {
				// Prepare
				$tax_yes_selected = 'checked="checked"';
				$opt_tax = $DW->getOptions($_GET['id'], $type . '-tax_' . $tax_type->name);
				if ( count($opt_tax) > 0 ) {
					$tax_act = array();

					foreach ( $opt_tax as $tax_condition ) {
						if ( $tax_condition['maintype'] == $type . '-tax_' . $tax_type->name ) {
							if ( $tax_condition['name'] == 'default' || empty($tax_condition['name']) ) {
								$tax_default = $tax_condition['value'];
							} else {
								$tax_act[ ] = $tax_condition['name'];
							}
						}
					}

					if ( $tax_default == '0' ) {
						$tax_no_selected = $tax_yes_selected;
						unset($tax_yes_selected);
					}
				}

				$tax = get_terms($tax_type->name, array('get' => 'all'));
				if ( count($tax) > 0 ) {
					if ( count($tax) > DW_LIST_LIMIT ) {
						$tax_condition_select_style = DW_LIST_STYLE;
					}

					echo '<br />';
					$DW->dumpOpt($opt_tax);
					echo __('Except for Custom Taxonomy') . ' <em>' . $tax_type->label . '</em>:<br />';
					echo '<div id="' . $type . '-tax_' . $tax_type->name . '-select" class="condition-select" ' . ( isset($tax_condition_select_style) ? $tax_condition_select_style : '' ) . '>';

					foreach ( $tax as $t ) {
						$run = TRUE;						
						if ( $DW->wpml ) {
							$wpml_id = dw_wpml_get_id($t->term_id, 'tax_' . $tax_type->name);
							if ( $wpml_id > 0 && $wpml_id <> $t->term_id ) {
								$run = FALSE;
							}
						}
						
						if ( $run ) {
							echo '<input type="checkbox" id="' . $type . '-tax_' . $tax_type->name . '_act_' . $t->term_id . '" name="' . $type . '-tax_' . $tax_type->name . '_act[]" value="' . $t->term_id . '" ';
							echo ( count($tax_act) > 0 && in_array($t->term_id, $tax_act) ) ? 'checked="checked"' : '';
							echo ' /> <label for="' . $type . '-tax_' . $tax_type->name . '_act_' . $t->term_id . '">';
							echo $t->name;
							echo '</label><br />';
						}
					}
					echo '</div>';
				}
			}

			echo '</div><!-- end dynwid_conf -->';
		}

		// Custom Post Type Archives
		if ( function_exists('is_post_type_archive') ) {
			$cp_archive_yes_selected = 'checked="checked"';
			$opt_cp_archive = $DW->getOptions($_GET['id'], 'cp_archive');
			if ( count($opt_cp_archive) > 0 ) {
				$cp_archive_act = array();
				foreach ( $opt_cp_archive as $cp_archive_condition ) {
					if ( $cp_archive_condition['name'] == 'default' || empty($cp_archive_condition['name']) ) {
						$cp_archive_default = $cp_archive_condition['value'];
					} else {
						$cp_archive_act[ ] = $cp_archive_condition['name'];
					}
				}

				if ( $cp_archive_default == '0' ) {
					$cp_archive_no_selected = $cp_archive_yes_selected;
					unset($cp_archive_yes_selected);
				}
			}

			if ( count($post_types) > DW_LIST_LIMIT ) {
				$cp_archive_condition_select_style = DW_LIST_STYLE;
			}

			echo '<h4><b>' . __('Custom Post Type Archives', DW_L10N_DOMAIN) . '</b> ' . ( count($opt_cp_archive) > 0 ? ' <img src="' . $DW->plugin_url . 'img/checkmark.gif" alt="Checkmark" />' : '' ) . '</h4>';
			echo '<div class="dynwid_conf">';
			echo __('Show widget on Custom Post Type Archives', DW_L10N_DOMAIN) . '?<br />';
			$DW->dumpOpt($opt_cp_archive);

			echo '<input type="radio" name="cp_archive" value="yes" id="cp_archive-yes" ' . ( isset($cp_archive_yes_selected) ? $cp_archive_yes_selected : '' ) . ' /> <label for="cp_archive-yes">' . __('Yes') . '</label> ';
			echo '<input type="radio" name="cp_archive" value="no" id="cp_archive-no" ' . ( isset($cp_archive_no_selected) ? $cp_archive_no_selected : '' ) . ' /> <label for="cp_archive-no">' . __('No') . '</label><br />';

			echo __('Except for', DW_L10N_DOMAIN) . ':<br />';
			echo '<div id="cp_archive-select" class="condition-select" ' . ( isset($cp_archive_condition_select_style) ? $cp_archive_condition_select_style : '' ) . '>';
			foreach ( $post_types as $type => $ctid ){
				echo '<input type="checkbox" id="cp_archive_act_' . $type . '" name="cp_archive_act[]" value="' . $type . '"' . ( count($cp_archive_act) > 0 && in_array($type, $cp_archive_act) ? 'checked="checked"' : '') . ' /> <label for="cp_archive_act_' . $type . '">' . $ctid->label . '</label><br />';
			}
			echo '</div>';
			echo '</div><!-- end dynwid_conf -->';
		}

	} // end version compare >= WP 3.0
?>

