<?php
/**
 * LSX Galleries Admin Class.
 *
 * @package lsx-galleries
 */
class LSX_Galleries_Admin {

	public $options = false;

	/**
	 * Construct method.
	 */
	public function __construct() {
		if ( ! class_exists( 'CMB_Meta_Box' ) ) {
			require_once( LSX_GALLERIES_PATH . '/vendor/Custom-Meta-Boxes/custom-meta-boxes.php' );
		}

		if ( function_exists( 'tour_operator' ) ) {
			$this->options = get_option( '_lsx-to_settings', false );
		} else {
			$this->options = get_option( '_lsx_settings', false );

			if ( false === $this->options ) {
				$this->options = get_option( '_lsx_lsx-settings', false );
			}
		}

		add_action( 'init', array( $this, 'post_type_setup' ) );
		add_action( 'init', array( $this, 'taxonomy_setup' ) );
		add_filter( 'cmb_meta_boxes', array( $this, 'field_setup' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'assets' ) );

		if ( is_admin() ) {
			add_filter( 'lsx_customizer_colour_selectors_body', array( $this, 'customizer_body_colours_handler' ), 15, 2 );
		}

		// add_action( 'init', array( $this, 'create_settings_page' ), 100 );
		// add_filter( 'lsx_framework_settings_tabs', array( $this, 'register_tabs' ), 100, 1 );

		// add_filter( 'type_url_form_media', array( $this, 'change_attachment_field_button' ), 20, 1 );
		add_filter( 'enter_title_here', array( $this, 'change_title_text' ) );

		add_filter( 'cf_custom_fields_pre_save_meta_key_to_post_type', array( $this, 'save_image_gallery_to_cmb' ), 10, 5 );
	}

	/**
	 * Register the Gallery post type.
	 */
	public function post_type_setup() {
		$labels = array(
			'name'               => esc_html_x( 'Galleries', 'post type general name', 'lsx-galleries' ),
			'singular_name'      => esc_html_x( 'Gallery', 'post type singular name', 'lsx-galleries' ),
			'add_new'            => esc_html_x( 'Add New', 'post type general name', 'lsx-galleries' ),
			'add_new_item'       => esc_html__( 'Add New Gallery', 'lsx-galleries' ),
			'edit_item'          => esc_html__( 'Edit Gallery', 'lsx-galleries' ),
			'new_item'           => esc_html__( 'New Gallery', 'lsx-galleries' ),
			'all_items'          => esc_html__( 'All Galleries', 'lsx-galleries' ),
			'view_item'          => esc_html__( 'View Gallery', 'lsx-galleries' ),
			'search_items'       => esc_html__( 'Search Galleries', 'lsx-galleries' ),
			'not_found'          => esc_html__( 'No galleries found', 'lsx-galleries' ),
			'not_found_in_trash' => esc_html__( 'No galleries found in Trash', 'lsx-galleries' ),
			'parent_item_colon'  => '',
			'menu_name'          => esc_html_x( 'Galleries', 'admin menu', 'lsx-galleries' ),
		);

		$args = array(
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'menu_icon'          => 'dashicons-admin-media',
			'query_var'          => true,
			'rewrite'            => array(
				'slug' => 'galleries',
			),
			'capability_type'    => 'post',
			'has_archive'        => 'galleries',
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => array(
				'title',
				'editor',
				'thumbnail',
				'excerpt',
			),
		);

		register_post_type( 'gallery', $args );
	}

	/**
	 * Register the Gallery Category taxonomy.
	 */
	public function taxonomy_setup() {
		$labels = array(
			'name'              => esc_html_x( 'Gallery Categories', 'taxonomy general name', 'lsx-galleries' ),
			'singular_name'     => esc_html_x( 'Category', 'taxonomy singular name', 'lsx-galleries' ),
			'search_items'      => esc_html__( 'Search Categories', 'lsx-galleries' ),
			'all_items'         => esc_html__( 'All Categories', 'lsx-galleries' ),
			'parent_item'       => esc_html__( 'Parent Category', 'lsx-galleries' ),
			'parent_item_colon' => esc_html__( 'Parent Category:', 'lsx-galleries' ),
			'edit_item'         => esc_html__( 'Edit Category', 'lsx-galleries' ),
			'update_item'       => esc_html__( 'Update Category', 'lsx-galleries' ),
			'add_new_item'      => esc_html__( 'Add New Category', 'lsx-galleries' ),
			'new_item_name'     => esc_html__( 'New Category Name', 'lsx-galleries' ),
			'menu_name'         => esc_html__( 'Categories', 'lsx-galleries' ),
		);

		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array(
				'slug' => 'galleries-category',
			),
		);

		register_taxonomy( 'gallery-category', array( 'gallery' ), $args );
	}

	/**
	 * Add metabox with custom fields to the Gallery post type.
	 */
	public function field_setup( $meta_boxes ) {
		$prefix = 'lsx_gallery_';

		// Global

		$fields = array(
			array(
				'name' => esc_html__( 'Featured:', 'lsx-galleries' ),
				'id'   => $prefix . 'featured',
				'type' => 'checkbox',
			),
		);

		$meta_boxes[] = array(
			'title'  => esc_html__( 'Gallery Details', 'lsx-galleries' ),
			'pages'  => 'gallery',
			'fields' => $fields,
		);

		// Uploader / User

		$fields = array(
			array(
				'name' => esc_html__( 'First Name:', 'lsx-galleries' ),
				'id'   => $prefix . 'first_name',
				'type' => 'text',
			),
			array(
				'name' => esc_html__( 'Last Name:', 'lsx-galleries' ),
				'id'   => $prefix . 'last_name',
				'type' => 'text',
			),
			array(
				'name' => esc_html__( 'Email Address:', 'lsx-galleries' ),
				'id'   => $prefix . 'email',
				'type' => 'text',
			),
			array(
				'name' => esc_html__( 'Phone Number:', 'lsx-galleries' ),
				'id'   => $prefix . 'phone',
				'type' => 'text',
			),
			array(
				'name' => esc_html__( 'Country of Residence', 'lsx-galleries' ),
				'id'   => $prefix . 'country',
				'type' => 'text',
			),
		);

		$meta_boxes[] = array(
			'title'  => esc_html__( 'Gallery Uploader Details', 'lsx-galleries' ),
			'pages'  => 'gallery',
			'fields' => $fields,
		);

		// Image 01

		$fields = array(
			array(
				'name' => esc_html__( 'Gallery image:', 'lsx-galleries' ),
				'id'   => $prefix . 'image_01_file',
				'type' => 'image',
				'desc' => esc_html__( 'Allowed Types: jpg,jpeg,png,gif, Suggested Size: 765x430 pixels (16:9)', 'lsx-galleries' ),
			),
			array(
				'id'   => $prefix . 'image_01_title',
				'name' => esc_html__( 'Gallery title', 'lsx-galleries' ),
				'type' => 'text',
			),
			array(
				'id'   => $prefix . 'image_01_description',
				'name' => esc_html__( 'Gallery description', 'lsx-galleries' ),
				'type' => 'textarea',
			),
		);

		$meta_boxes[] = array(
			'title'  => esc_html__( 'Gallery Image 01', 'lsx-galleries' ),
			'pages'  => 'gallery',
			'fields' => $fields,
		);

		// Image 02

		$fields = array(
			array(
				'name' => esc_html__( 'Gallery image:', 'lsx-galleries' ),
				'id'   => $prefix . 'image_02_file',
				'type' => 'image',
				'desc' => esc_html__( 'Allowed Types: jpg,jpeg,png,gif, Suggested Size: 765x430 pixels (16:9)', 'lsx-galleries' ),
			),
			array(
				'id'   => $prefix . 'image_02_title',
				'name' => esc_html__( 'Gallery title', 'lsx-galleries' ),
				'type' => 'text',
			),
			array(
				'id'   => $prefix . 'image_02_description',
				'name' => esc_html__( 'Gallery description', 'lsx-galleries' ),
				'type' => 'textarea',
			),
		);

		$meta_boxes[] = array(
			'title'  => esc_html__( 'Gallery Image 02', 'lsx-galleries' ),
			'pages'  => 'gallery',
			'fields' => $fields,
		);

		// Image 03

		$fields = array(
			array(
				'name' => esc_html__( 'Gallery image:', 'lsx-galleries' ),
				'id'   => $prefix . 'image_03_file',
				'type' => 'image',
				'desc' => esc_html__( 'Allowed Types: jpg,jpeg,png,gif, Suggested Size: 765x430 pixels (16:9)', 'lsx-galleries' ),
			),
			array(
				'id'   => $prefix . 'image_03_title',
				'name' => esc_html__( 'Gallery title', 'lsx-galleries' ),
				'type' => 'text',
			),
			array(
				'id'   => $prefix . 'image_03_description',
				'name' => esc_html__( 'Gallery description', 'lsx-galleries' ),
				'type' => 'textarea',
			),
		);

		$meta_boxes[] = array(
			'title'  => esc_html__( 'Gallery Image 03', 'lsx-galleries' ),
			'pages'  => 'gallery',
			'fields' => $fields,
		);

		return $meta_boxes;
	}

	/**
	 * Enqueue JS and CSS.
	 */
	public function assets( $hook ) {
		// wp_enqueue_media();
		wp_enqueue_script( 'media-upload' );
		wp_enqueue_script( 'thickbox' );
		wp_enqueue_style( 'thickbox' );

		wp_enqueue_script( 'lsx-galleries-admin', LSX_GALLERIES_URL . 'assets/js/lsx-galleries-admin.min.js', array( 'jquery' ), LSX_GALLERIES_VER, true );
		wp_enqueue_style( 'lsx-galleries-admin', LSX_GALLERIES_URL . 'assets/css/lsx-galleries-admin.css', array(), LSX_GALLERIES_VER );
	}

	/**
	 * Handle body colours that might be change by LSX Customiser.
	 */
	public function customizer_body_colours_handler( $css, $colors ) {
		$css .= '
			@import "' . LSX_GALLERIES_PATH . '/assets/css/scss/customizer-galleries-body-colours";

			/**
			 * LSX Customizer - Body (LSX Galleries)
			 */
			@include customizer-galleries-body-colours (
				$bg: 		' . $colors['background_color'] . ',
				$breaker: 	' . $colors['body_line_color'] . ',
				$color:    	' . $colors['body_text_color'] . ',
				$link:    	' . $colors['body_link_color'] . ',
				$hover:    	' . $colors['body_link_hover_color'] . ',
				$small:    	' . $colors['body_text_small_color'] . '
			);
		';

		return $css;
	}

	/**
	 * Change the Gallery post title.
	 */
	public function change_title_text( $title ) {
		$screen = get_current_screen();

		if ( 'gallery' === $screen->post_type ) {
			$title = esc_attr__( 'Enter gallery title', 'lsx-galleries' );
		}

		return $title;
	}

	/**
	 * Save the image ID (and not image URL) on image meta.
	 */
	public function save_image_gallery_to_cmb( $value, $slug, $entry_id, $field, $form ) {
		global $wpdb;

		if ( in_array( $slug, array( 'lsx_gallery_image_01_file', 'lsx_gallery_image_02_file', 'lsx_gallery_image_03_file' ) ) ) {
			$media = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE guid='%s';", $value ) );

			if ( ! empty( $media ) && ! empty( $media[0] ) ) {
				return $media[0];
			}
		}

		return $value;
	}

}

global $lsx_galleries_admin;
$lsx_galleries_admin = new LSX_Galleries_Admin();
