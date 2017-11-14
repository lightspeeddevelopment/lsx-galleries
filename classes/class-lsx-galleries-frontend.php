<?php
/**
 * LSX Galleries Frontend Class.
 *
 * @package lsx-galleries
 */
class LSX_Galleries_Frontend {

	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'assets' ), 999 );
	}

	public function assets() {
		wp_enqueue_script( 'lsx-galleries', LSX_GALLERIES_URL . 'assets/js/lsx-galleries.min.js', array( 'jquery' ), LSX_GALLERIES_VER, true );

		$params = apply_filters( 'lsx_galleries_js_params', array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
		));

		wp_localize_script( 'lsx-galleries', 'lsx_customizer_params', $params );

		wp_enqueue_style( 'lsx-galleries', LSX_GALLERIES_URL . 'assets/css/lsx-galleries.css', array(), LSX_GALLERIES_VER );
		wp_style_add_data( 'lsx-galleries', 'rtl', 'replace' );
	}

}

global $lsx_galleries_frontend;
$lsx_galleries_frontend = new LSX_Galleries_Frontend();
