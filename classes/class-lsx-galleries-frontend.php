<?php
/**
 * LSX Galleries Frontend Class.
 *
 * @package lsx-galleries
 */
class LSX_Galleries_Frontend {

	public $options = false;

	/**
	 * Construct method.
	 */
	public function __construct() {
		if ( function_exists( 'tour_operator' ) ) {
			$this->options = get_option( '_lsx-to_settings', false );
		} else {
			$this->options = get_option( '_lsx_settings', false );

			if ( false === $this->options ) {
				$this->options = get_option( '_lsx_lsx-settings', false );
			}
		}

		add_action( 'wp_enqueue_scripts', array( $this, 'assets' ), 999 );
		add_action( 'wp_footer', array( $this, 'add_gallery_modal' ) );

		add_action( 'wp_ajax_get_gallery_embed', array( $this, 'get_gallery_embed' ) );
		add_action( 'wp_ajax_nopriv_get_gallery_embed', array( $this, 'get_gallery_embed' ) );

		add_filter( 'wp_kses_allowed_html', array( $this, 'wp_kses_allowed_html' ), 10, 2 );
		add_filter( 'template_include', array( $this, 'single_template_include' ), 99 );
		add_filter( 'template_include', array( $this, 'archive_template_include' ), 99 );

		add_filter( 'lsx_banner_title', array( $this, 'lsx_banner_archive_title' ), 15 );

		add_filter( 'excerpt_more_p', array( $this, 'change_excerpt_more' ) );
		add_filter( 'excerpt_length', array( $this, 'change_excerpt_length' ) );
		add_filter( 'excerpt_strip_tags', array( $this, 'change_excerpt_strip_tags' ) );

		add_action( 'lsx_content_top', array( $this, 'categories_tabs' ), 15 );
	}

	/**
	 * Enqueue JS and CSS.
	 */
	public function assets() {
		$has_slick = wp_script_is( 'slick', 'queue' );

		if ( ! $has_slick ) {
			wp_enqueue_style( 'slick', LSX_GALLERIES_URL . 'assets/css/vendor/slick.css', array(), LSX_GALLERIES_URL, null );
			wp_enqueue_script( 'slick', LSX_GALLERIES_URL . 'assets/js/vendor/slick.min.js', array( 'jquery' ), null, LSX_GALLERIES_URL, true );
		}

		wp_enqueue_script( 'lsx-galleries', LSX_GALLERIES_URL . 'assets/js/lsx-galleries.min.js', array( 'jquery', 'slick' ), LSX_GALLERIES_VER, true );

		$params = apply_filters( 'lsx_galleries_js_params', array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
		));

		wp_localize_script( 'lsx-galleries', 'lsx_galleries_params', $params );

		wp_enqueue_style( 'lsx-galleries', LSX_GALLERIES_URL . 'assets/css/lsx-galleries.css', array( 'slick' ), LSX_GALLERIES_VER );
		wp_style_add_data( 'lsx-galleries', 'rtl', 'replace' );
	}

	/**
	 * Add gallery modal.
	 */
	public function add_gallery_modal() {
		?>
		<div class="lsx-modal modal fade" id="lsx-galleries-modal" role="dialog">
			<div class="modal-dialog">
				<div class="modal-content">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<div class="modal-header">
						<h4 class="modal-title"></h4>
					</div>
					<div class="modal-body"></div>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Get gallery embed (ajax).
	 */
	public function get_gallery_embed() {
		echo do_shortcode( '[gallery width="992" height="558" src="' . $gallery . '"]' );
		// if ( ! empty( $_GET['gallery'] ) && ! empty( $_GET['post_id'] ) ) {
		// 	$gallery = sanitize_text_field( wp_unslash( $_GET['gallery'] ) );
		// 	$post_id = sanitize_text_field( wp_unslash( $_GET['post_id'] ) );

		// 	if ( ! empty( $gallery ) && ! empty( $post_id ) ) {
		// 		$this->increase_views_counter( $post_id );
		// 		$gallery_parts = parse_url( $gallery );

		// 		echo '<div class="embed-responsive embed-responsive-16by9">';

		// 		// if ( in_array( $gallery_parts['host'], array( 'www.youtube.com', 'youtube.com', 'youtu.be' ) ) ) {
		// 		// 	// @codingStandardsIgnoreLine
		// 		// 	echo wp_oembed_get( $gallery, array(
		// 		// 		'height' => 558,
		// 		// 		'width' => 992,
		// 		// 	) );
		// 		// } else {
		// 		// 	echo do_shortcode( '[gallery width="992" height="558" src="' . $gallery . '"]' );
		// 		// }

		// 		echo do_shortcode( '[gallery width="992" height="558" src="' . $gallery . '"]' );

		// 		echo '</div>';
		// 	}
		// }

		// echo '<div>';

		// 	if ( ! empty( $_GET['gallery'] ) && ! empty( $_GET['post_id'] ) ) {
		// 	$img_1_figure = get_post_meta( get_the_ID(), 'lsx_gallery_image_01_file', true );
		// 		if ( ! empty( $img_1_figure ) ) {
		// 			echo '<img src="' .wp_get_attachment_url( $img_1_figure ). '">';
		// 		}
		// 	$img_1_title = get_post_meta( get_the_ID(), 'lsx_gallery_image_01_title', true );
		// 		if ( ! empty( $img_1_title ) ) {
		// 			echo '<p>Image title: '.$img_1_title.'</p>';
		// 		}
		// 	$img_1_description = get_post_meta( get_the_ID(), 'lsx_gallery_image_01_description', true );
		// 		if ( ! empty( $img_1_description ) ) {
		// 			echo '<p>Image description: '.$img_1_description.'</p>';
		// 		}
		// 	}
		// echo '</div>';

		wp_die();
	}

	/**
	 * Increase gallery views counter.
	 */
	// public function increase_views_counter( $post_id ) {
	// 	$count = (int) get_post_meta( $post_id, '_views', true );
	// 	$count++;
	// 	update_post_meta( $post_id, '_views', $count );
	// }

	/**
	 * Allow data params for Slick slider addon.
	 * Allow data params for Bootstrap modal.
	 */
	public function wp_kses_allowed_html( $allowedtags, $context ) {
		$allowedtags['div']['data-slick'] = true;
		$allowedtags['a']['data-toggle'] = true;
		$allowedtags['a']['data-gallery'] = true;
		$allowedtags['a']['data-post-id'] = true;
		$allowedtags['a']['data-title'] = true;
		return $allowedtags;
	}

	/**
	 * Single template.
	 */
	public function single_template_include( $template ) {
		if ( is_main_query() && is_singular( 'gallery' ) ) {
			if ( empty( locate_template( array( 'single-galleries.php' ) ) ) && file_exists( LSX_GALLERIES_PATH . 'templates/single-galleries.php' ) ) {
				$template = LSX_GALLERIES_PATH . 'templates/single-galleries.php';
			}
		}

		return $template;
	}

	/**
	 * Archive template.
	 */
	public function archive_template_include( $template ) {
		if ( is_main_query() && ( is_post_type_archive( 'gallery' ) || is_tax( 'gallery-category' ) ) ) {
			if ( empty( locate_template( array( 'archive-galleries.php' ) ) ) && file_exists( LSX_GALLERIES_PATH . 'templates/archive-galleries.php' ) ) {
				$template = LSX_GALLERIES_PATH . 'templates/archive-galleries.php';
			}
		}

		return $template;
	}

	/**
	 * Change the LSX Banners title for galleries archive.
	 */
	public function lsx_banner_archive_title( $title ) {
		if ( is_main_query() && is_post_type_archive( 'gallery' ) ) {
			$title = '<h1 class="page-title">' . esc_html__( 'Galleries', 'lsx-galleries' ) . '</h1>';
		}

		if ( is_main_query() && is_tax( 'gallery-category' ) ) {
			$tax = get_queried_object();
			$title = '<h1 class="page-title">' . esc_html__( 'Galleries Category', 'lsx-galleries' ) . ': ' . apply_filters( 'the_title', $tax->name ) . '</h1>';
		}

		return $title;
	}

	/**
	 * Remove the "continue reading".
	 */
	public function change_excerpt_more( $excerpt_more ) {
		global $post;

		if ( 'gallery' === $post->post_type ) {
			$youtube_url = get_post_meta( $post->ID, 'lsx_gallery_youtube', true );
			$gallery_id = get_post_meta( $post->ID, 'lsx_gallery_gallery', true );

			if ( ! empty( $youtube_url ) ) {
				$gallery_url = $youtube_url;
			} elseif ( ! empty( $gallery_id ) ) {
				$gallery_url = wp_get_attachment_url( $gallery_id );
			}

			$excerpt_more = '<p><a href="#lsx-galleries-modal" data-toggle="modal" data-post-id="' . esc_attr( $post->ID ) . '" data-gallery="' . esc_url( $gallery_url ) . '" data-title="' . the_title( '', '', false ) . '" class="moretag">' . esc_html__( 'View gallery', 'lsx' ) . '</a></p>';
		}

		return $excerpt_more;
	}

	/**
	 * Change the word count when crop the content to excerpt.
	 */
	public function change_excerpt_length( $excerpt_word_count ) {
		global $post;

		if ( is_front_page() && 'gallery' === $post->post_type ) {
			$excerpt_word_count = 20;
		}

		if ( is_singular( 'gallery' ) ) {
			$excerpt_word_count = 20;
		}

		return $excerpt_word_count;
	}

	/**
	 * Change the allowed tags crop the content to excerpt.
	 */
	public function change_excerpt_strip_tags( $allowed_tags ) {
		global $post;

		if ( is_front_page() && 'gallery' === $post->post_type ) {
			$allowed_tags = '<p>,<br>,<b>,<strong>,<i>,<u>,<ul>,<ol>,<li>,<span>';
		}

		if ( is_singular( 'gallery' ) ) {
			$allowed_tags = '<p>,<br>,<b>,<strong>,<i>,<u>,<ul>,<ol>,<li>,<span>';
		}

		return $allowed_tags;
	}

	/**
	 * Display categories tabs.
	 */
	public function categories_tabs() {
		if ( is_post_type_archive( 'gallery' ) || is_tax( 'gallery-category' ) ) :
			$args = array(
				'taxonomy'   => 'gallery-category',
				'hide_empty' => false,
			);

			$categories = get_terms( $args );
			$category_selected = get_query_var( 'gallery-category' );

			if ( count( $categories ) > 0 ) :
				?>

				<ul class="nav nav-tabs lsx-galleries-filter">
					<?php
						$category_selected_class = '';

						if ( empty( $category_selected ) ) {
							$category_selected_class = ' class="active"';
						}
					?>

					<li<?php echo wp_kses_post( $category_selected_class ); ?>><a href="<?php echo esc_url( get_post_type_archive_link( 'gallery' ) ); ?>" data-filter="*"><?php esc_html_e( 'All', 'lsx-galleries' ); ?></a></li>

					<?php foreach ( $categories as $category ) : ?>
						<?php
							$category_selected_class = '';

							if ( (string) $category_selected === (string) $category->slug ) {
								$category_selected_class = ' class="active"';
							}
						?>

						<li<?php echo wp_kses_post( $category_selected_class ); ?>><a href="<?php echo esc_url( get_term_link( $category ) ); ?>" data-filter=".filter-<?php echo esc_attr( $category->slug ); ?>"><?php echo esc_attr( $category->name ); ?></a></li>
					<?php endforeach; ?>
				</ul>

				<?php
			endif;
		endif;
	}

}

global $lsx_galleries_frontend;
$lsx_galleries_frontend = new LSX_Galleries_Frontend();
