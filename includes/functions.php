<?php
/**
 * LSX Galleries functions.
 *
 * @package lsx-galleries
 */

/**
 * Adds text domain.
 */
function lsx_galleries_load_plugin_textdomain() {
	load_plugin_textdomain( 'lsx-galleries', false, basename( LSX_GALLERIES_PATH ) . '/languages' );
}
add_action( 'init', 'lsx_galleries_load_plugin_textdomain' );
