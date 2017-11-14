<?php
/**
 * LSX Galleries Admin Class.
 *
 * @package lsx-galleries
 */
class LSX_Galleries_Admin {

	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'assets' ) );
	}

	public function assets() {
		//wp_enqueue_media();
		wp_enqueue_script( 'media-upload' );
		wp_enqueue_script( 'thickbox' );
		wp_enqueue_style( 'thickbox' );

		wp_enqueue_script( 'lsx-galleries-admin', LSX_GALLERIES_URL . 'assets/js/lsx-galleries-admin.min.js', array( 'jquery' ), LSX_GALLERIES_VER, true );
		wp_enqueue_style( 'lsx-galleries-admin', LSX_GALLERIES_URL . 'assets/css/lsx-galleries-admin.css', array(), LSX_GALLERIES_VER );
	}

}

global $lsx_galleries_admin;
$lsx_galleries_admin = new LSX_Galleries_Admin();
