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

/**
 * Wraps the output class in a function to be called in templates.
 */
function lsx_galleries( $args ) {
	$lsx_galleries = new LSX_Galleries;
	echo wp_kses_post( $lsx_galleries->output( $args ) );
}

/**
 * Wraps the output class in a function to be called in templates.
 */
function lsx_galleries_most_recent( $args ) {
	$lsx_galleries = new LSX_Galleries;
	echo wp_kses_post( $lsx_galleries->output_most_recent( $args ) );
}

/**
 * Wraps the output class in a function to be called in templates.
 */
function lsx_galleries_categories( $args ) {
	$lsx_galleries = new LSX_Galleries;
	echo wp_kses_post( $lsx_galleries->output_categories( $args ) );
}

/**
 * Shortcode [lsx_galleries].
 */
function lsx_galleries_shortcode( $atts ) {
	$lsx_galleries = new LSX_Galleries;
	return $lsx_galleries->output( $atts );
}
add_shortcode( 'lsx_galleries', 'lsx_galleries_shortcode' );

/**
 * Shortcode [lsx_galleries_most_recent].
 */
function lsx_galleries_most_recent_shortcode( $atts ) {
	$lsx_galleries = new LSX_Galleries;
	return $lsx_galleries->output_most_recent( $atts );
}
add_shortcode( 'lsx_galleries_most_recent', 'lsx_galleries_most_recent_shortcode' );

/**
 * Shortcode [lsx_galleries_categories].
 */
function lsx_galleries_categories_shortcode( $atts ) {
	$lsx_galleries = new LSX_Galleries;
	return $lsx_galleries->output_categories( $atts );
}
add_shortcode( 'lsx_galleries_categories', 'lsx_galleries_categories_shortcode' );
