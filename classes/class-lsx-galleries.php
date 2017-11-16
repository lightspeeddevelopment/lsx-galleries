<?php
/**
 * LSX Galleries Main Class.
 *
 * @package lsx-galleries
 */
class LSX_Galleries {

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

		add_filter( 'lsx_banner_allowed_post_types', array( $this, 'lsx_banner_allowed_post_types' ) );
		add_filter( 'lsx_banner_allowed_taxonomies', array( $this, 'lsx_banner_allowed_taxonomies' ) );
	}

	/**
	 * Enable gallery custom post type on LSX Banners.
	 */
	public function lsx_banner_allowed_post_types( $post_types ) {
		$post_types[] = 'gallery';
		return $post_types;
	}

	/**
	 * Enable gallery custom taxonomies on LSX Banners.
	 */
	public function lsx_banner_allowed_taxonomies( $taxonomies ) {
		$taxonomies[] = 'gallery-category';
		return $taxonomies;
	}

	/**
	 * Enable custom image sizes.
	 */
	public function custom_image_sizes( $post_types ) {
		add_image_size( 'lsx-galleries-cover', 765, 420, true ); // 16:9
	}

	/**
	 * Returns the shortcode output markup.
	 */
	public function output( $atts ) {
		// @codingStandardsIgnoreLine
		extract( shortcode_atts( array(
			'columns' => 3,
			'orderby' => 'name',
			'order' => 'ASC',
			'limit' => '-1',
			'include' => '',
			'category' => '',
			'display' => 'excerpt',
			'size' => 'lsx-thumbnail-single',
			'carousel' => 'true',
			'featured' => 'false',
		), $atts ) );

		$output = '';

		if ( ! empty( $include ) ) {
			$include = explode( ',', $include );

			$args = array(
				'post_type' => 'gallery',
				'posts_per_page' => $limit,
				'post__in' => $include,
				'orderby' => 'post__in',
				'order' => $order,
			);
		} else {
			$args = array(
				'post_type' => 'gallery',
				'posts_per_page' => $limit,
				'orderby' => $orderby,
				'order' => $order,
			);

			if ( 'true' === $featured || true === $featured ) {
				$args['meta_key'] = 'lsx_gallery_featured';
				$args['meta_value'] = 1;
			}

			if ( ! empty( $category ) ) {
				$args['tax_query'] = array(
					array(
						'taxonomy' => 'gallery-category',
						'field' => 'slug',
						'terms' => array( $category ),
					),
				);
			}
		}

		$galleries = new \WP_Query( $args );

		if ( $galleries->have_posts() ) {
			global $post;

			$count = 0;
			$count_global = 0;

			if ( 'true' === $carousel || true === $carousel ) {
				$output .= '<div class="lsx-galleries-shortcode lsx-galleries-slider" data-slick=\'{"slidesToShow": ' . $columns . ', "slidesToScroll": ' . $columns . '}\'>';
			} else {
				$output .= '<div class="lsx-galleries-shortcode"><div class="row">';
			}

			while ( $galleries->have_posts() ) {
				$galleries->the_post();

				$count++;
				$count_global++;

				$gallery_id = get_post_meta( $post->ID, 'lsx_gallery_gallery', true );

				$gallery_meta = get_post_meta( $gallery_id , '_wp_attachment_metadata', true );


				if ( 'full' === $display ) {
					$content = apply_filters( 'the_content', get_the_content() );
					$content = str_replace( ']]>', ']]&gt;', $content );
				} elseif ( 'excerpt' === $display ) {
					$content = apply_filters( 'the_excerpt', get_the_excerpt() );
				} elseif ( 'none' === $display ) {
					$content = '<a href="#lsx-galleries-modal" data-toggle="modal" data-post-id="' . esc_attr( $post->ID ) . '" data-gallery="' . esc_url( $galery_url ) . '" data-title="' . apply_filters( 'the_title', $post->post_title ) . '" class="moretag">' . esc_html__( 'View gallery', 'lsx-galleries' ) . '</a>';
				}

				if ( is_numeric( $size ) ) {
					$thumb_size = array( $size, $size );
				} else {
					$thumb_size = $size;
				}

				if ( ! empty( get_the_post_thumbnail( $post->ID ) ) ) {
					$image = get_the_post_thumbnail( $post->ID, $thumb_size, array(
						'class' => 'img-responsive',
					) );
				} else {
					$image = '';
				}

				if ( empty( $image ) ) {
					if ( $this->options['display'] && ! empty( $this->options['display']['galleries_placeholder'] ) ) {
						$image = '<img class="img-responsive" src="' . $this->options['display']['galleries_placeholder'] . '" alt="placeholder">';
					} else {
						$image = '';
					}
				}

				$categories = '';
				$terms = get_the_terms( $post->ID, 'gallery-category' );

				if ( $terms && ! is_wp_error( $terms ) ) {
					$categories = array();

					foreach ( $terms as $term ) {
						$categories[] = '<a href="' . get_term_link( $term ) . '">' . $term->name . '</a>';
					}

					$categories = join( ', ', $categories );
				}

				$gallery_categories = '' !== $categories ? ( '<p class="lsx-galleries-categories">' . $categories . '</p>' ) : '';

				if ( 'true' === $carousel || true === $carousel ) {
					$output .= '
						<div class="lsx-galleries-slot">
							' . ( ! empty( $image ) ? '<a href="#lsx-galleries-modal" data-toggle="modal" data-post-id="' . esc_attr( $post->ID ) . '" data-gallery="' . esc_url( $gallery_url ) . '" data-title="' . apply_filters( 'the_title', $post->post_title ) . '"><figure class="lsx-galleries-avatar">' . $image . '</figure></a>' : '' ) . '
							<h5 class="lsx-galleries-title"><a href="' . get_page_link( $the_ID ) . '" data-toggle="modal" data-post-id="' . esc_attr( $post->ID ) . '" data-gallery="' . esc_url( $gallery_url ) . '" data-title="' . apply_filters( 'the_title', $post->post_title ) . '">' . apply_filters( 'the_title', $post->post_title ) . '</a></h5>
							' . $gallery_categories . '

							<div class="lsx-galleries-content">' . $content . '</div>
						</div>';
				} elseif ( $columns >= 1 && $columns <= 4 ) {
					$md_col_width = 12 / $columns;

					$output .= '
						<div class="col-xs-12 col-md-' . $md_col_width . '">
							<div class="lsx-galleries-slot">
								' . ( ! empty( $image ) ? '<a href="#lsx-galleries-modal" data-toggle="modal" data-post-id="' . esc_attr( $post->ID ) . '" data-gallery="' . esc_url( $gallery_url ) . '" data-title="' . apply_filters( 'the_title', $post->post_title ) . '"><figure class="lsx-galleries-avatar">' . $image . '</figure></a>' : '' ) . '
								<h5 class="lsx-galleries-title"><a href="' . get_page_link( $the_ID ) . '" data-toggle="modal" data-post-id="' . esc_attr( $post->ID ) . '" data-gallery="' . esc_url( $gallery_url ) . '" data-title="' . apply_filters( 'the_title', $post->post_title ) . '">' . apply_filters( 'the_title', $post->post_title ) . '</a></h5>
								' . $gallery_categories . '

								<div class="lsx-galleries-content">' . $content . '</div>
							</div>
						</div>';

					if ( $count == $columns && $galleries->post_count > $count_global ) {
						$output .= '</div>';
						$output .= '<div class="row">';
						$count = 0;
					}
				} else {
					$output .= '
						<div class="alert alert-danger">
							' . esc_html__( 'Invalid number of columns set. LSX Galleries supports 1 to 4 columns.', 'lsx-galleries' ) . '
						</div>';
				}

				wp_reset_postdata();
			}

			if ( 'true' !== $carousel && true !== $carousel ) {
				$output .= '</div>';
			}

			$output .= '</div>';

			return $output;
		}
	}

	/**
	 * Returns the shortcode output markup.
	 */
	public function output_most_recent( $atts ) {
		// @codingStandardsIgnoreLine
		extract( shortcode_atts( array(
			'include' => '',
			'display' => 'excerpt',
			'size' => 'lsx-thumbnail-single',
			'featured' => 'false',
		), $atts ) );

		$output = '';

		if ( ! empty( $include ) ) {
			$args = array(
				'post_type' => 'gallery',
				'posts_per_page' => 1,
				'post__in' => array( $include ),
				'orderby' => 'post__in',
				'order' => 'DESC',
			);
		} else {
			$args = array(
				'post_type' => 'gallery',
				'posts_per_page' => 1,
				'orderby' => 'date',
				'order' => 'DESC',
			);

			if ( 'true' === $featured || true === $featured ) {
				$args['meta_key'] = 'lsx_gallery_featured';
				$args['meta_value'] = 1;
			}
		}

		$galleries = new \WP_Query( $args );

		if ( $galleries->have_posts() ) {
			global $post;

			$output .= '<div class="lsx-galleries-shortcode lsx-galleries-most-recent-shortcode"><div class="row">';

			while ( $galleries->have_posts() ) {
				$galleries->the_post();

				$gallery_id = get_post_meta( $post->ID, 'lsx_gallery_gallery', true );

				$gallery_meta = get_post_meta( $gallery_id , '_wp_attachment_metadata', true );

				if ( 'full' === $display ) {
					$content = apply_filters( 'the_content', get_the_content() );
					$content = str_replace( ']]>', ']]&gt;', $content );
				} elseif ( 'excerpt' === $display ) {
					$content = apply_filters( 'the_excerpt', get_the_excerpt() );
				} elseif ( 'none' === $display ) {
					$content = '<a href="#lsx-galleries-modal" data-toggle="modal" data-post-id="' . esc_attr( $post->ID ) . '" data-gallery="' . esc_url( $gallery_url ) . '" data-title="' . apply_filters( 'the_title', $post->post_title ) . '" class="moretag">' . esc_html__( 'View gallery', 'lsx-galleries' ) . '</a>';
				}

				if ( is_numeric( $size ) ) {
					$thumb_size = array( $size, $size );
				} else {
					$thumb_size = $size;
				}

				if ( ! empty( get_the_post_thumbnail( $post->ID ) ) ) {
					$image = get_the_post_thumbnail( $post->ID, $thumb_size, array(
						'class' => 'img-responsive',
					) );
				} else {
					$image = '';
				}

				if ( empty( $image ) ) {
					if ( ! empty( $this->options['display'] ) && ! empty( $this->options['display']['galleries_placeholder'] ) ) {
						$image = '<img class="img-responsive" src="' . $this->options['display']['galleries_placeholder'] . '" alt="placeholder">';
					}
				}

				$categories = '';
				$terms = get_the_terms( $post->ID, 'gallery-category' );

				if ( $terms && ! is_wp_error( $terms ) ) {
					$categories = array();

					foreach ( $terms as $term ) {
						$categories[] = '<a href="' . get_term_link( $term ) . '">' . $term->name . '</a>';
					}

					$categories = join( ', ', $categories );
				}

				$gallery_categories = '' !== $categories ? ( '<p class="lsx-galleries-categories">' . $categories . '</p>' ) : '';

				if ( ! empty( $gallery_meta ) && ! empty( $gallery_meta['length_formatted'] ) ) {
					$length = $gallery_meta['length_formatted'];
					$meta = $length . ' | ' . $meta;
				}

				/* Translators: 1: time ago (gallery published date) */
				$meta .= ' | ' . sprintf( esc_html__( '%1$s ago', 'lsx-galleries' ), human_time_diff( get_the_time( 'U' ), current_time( 'timestamp' ) ) );

				$output .= '
					<div class="col-xs-12 col-md-6">
						<div class="lsx-galleries-slot-single">
							' . ( ! empty( $image ) ? '<a href="#lsx-galleries-modal" data-toggle="modal" data-post-id="' . esc_attr( $post->ID ) . '" data-gallery="' . esc_url( $gallery_url ) . '" data-title="' . apply_filters( 'the_title', $post->post_title ) . '"><figure class="lsx-galleries-avatar">' . $image . '</figure></a>' : '' ) . '
						</div>
					</div>
					<div class="col-xs-12 col-md-6">
						<div class="lsx-galleries-slot-single">
							<h5 class="lsx-galleries-title"><a href="' . get_page_link( $the_ID ) . '" data-toggle="modal" data-post-id="' . esc_attr( $post->ID ) . '" data-gallery="' . esc_url( $gallery_url ) . '" data-title="' . apply_filters( 'the_title', $post->post_title ) . '">' . apply_filters( 'the_title', $post->post_title ) . '</a></h5>
							' . $gallery_categories . '

							<div class="lsx-galleries-content">' . $content . '</div>
						</div>
					</div>';

				wp_reset_postdata();
			}

			$output .= '</div></div>';

			return $output;
		}
	}

	/**
	 * Returns the shortcode output markup.
	 */
	public function output_categories( $atts ) {
		// @codingStandardsIgnoreLine
		extract( shortcode_atts( array(
			'columns' => 3,
			'orderby' => 'name',
			'order' => 'ASC',
			'limit' => '-1',
			'include' => '',
			'display' => 'excerpt',
			'size' => 'lsx-thumbnail-single',
			'carousel' => 'true',
		), $atts ) );

		$output = '';

		if ( ! empty( $include ) ) {
			$include = explode( ',', $include );

			$args = array(
				'taxonomy' => 'gallery-category',
				'number' => (int) $limit,
				'include' => $include,
				'orderby' => 'include',
				'order' => $order,
				'hide_empty' => 0,
			);
		} else {
			$args = array(
				'taxonomy' => 'gallery-category',
				'number' => (int) $limit,
				'orderby' => $orderby,
				'order' => $order,
				'hide_empty' => 0,
			);
		}

		if ( 'none' !== $orderby ) {
			$args['suppress_filters']           = true;
			$args['disabled_custom_post_order'] = true;
		}

		$gallery_categories = get_terms( $args );

		if ( ! empty( $gallery_categories ) && ! is_wp_error( $gallery_categories ) ) {
			global $post;

			$count = 0;
			$count_global = 0;

			if ( 'true' === $carousel || true === $carousel ) {
				$output .= '<div class="lsx-galleries-shortcode lsx-galleries-slider" data-slick=\'{"slidesToShow": ' . $columns . ', "slidesToScroll": ' . $columns . '}\'>';
			} else {
				$output .= '<div class="lsx-galleries-shortcode"><div class="row">';
			}

			foreach ( $gallery_categories as $term ) {
				$count++;
				$count_global++;

				$content = '<p><a href="' . get_term_link( $term, 'gallery-category' ) . '" class="moretag">' . esc_html__( 'View more', 'lsx-galleries' ) . '</a></p>';

				if ( 'none' !== $display ) {
					$content = apply_filters( 'term_description', $term->description ) . $content;
				}

				if ( is_numeric( $size ) ) {
					$thumb_size = array( $size, $size );
				} else {
					$thumb_size = $size;
				}

				$term_image_id = get_term_meta( $term->term_id, 'thumbnail', true );
				$image = wp_get_attachment_image_src( $term_image_id, $thumb_size );

				if ( ! empty( $image ) ) {
					$image = '<img class="img-responsive" src="' . $image[0] . '" alt="' . $term->name . '">';
				} else {
					if ( $this->options['display'] && ! empty( $this->options['display']['galleries_placeholder'] ) ) {
						$image = '<img class="img-responsive" src="' . $this->options['display']['galleries_placeholder'] . '" alt="placeholder">';
					} else {
						$image = '';
					}
				}

				if ( 'true' === $carousel || true === $carousel ) {
					$output .= '
						<div class="lsx-galleries-slot">
							' . ( ! empty( $image ) ? '<a href="' . get_term_link( $term, 'gallery-category' ) . '"><figure class="lsx-galleries-avatar">' . $image . '</figure></a>' : '' ) . '
							<h5 class="lsx-galleries-title"><a href="' . get_term_link( $term, 'gallery-category' ) . '">' . apply_filters( 'the_title', $term->name ) . '</a></h5>
							<div class="lsx-galleries-content">' . $content . '</div>
						</div>';
				} elseif ( $columns >= 1 && $columns <= 4 ) {
					$md_col_width = 12 / $columns;

					$output .= '
						<div class="col-xs-12 col-md-' . $md_col_width . '">
							<div class="lsx-galleries-slot">
								' . ( ! empty( $image ) ? '<a href="' . get_term_link( $term, 'gallery-category' ) . '"><figure class="lsx-galleries-avatar">' . $image . '</figure></a>' : '' ) . '
								<h5 class="lsx-galleries-title"><a href="' . get_term_link( $term, 'gallery-category' ) . '">' . apply_filters( 'the_title', $term->name ) . '</a></h5>
								<div class="lsx-galleries-content">' . $content . '</div>
							</div>
						</div>';

					if ( $count == $columns && $galleries->post_count > $count_global ) {
						$output .= '</div>';
						$output .= '<div class="row">';
						$count = 0;
					}
				} else {
					$output .= '
						<div class="alert alert-danger">
							' . esc_html__( 'Invalid number of columns set. LSX Galleries supports 1 to 4 columns.', 'lsx-galleries' ) . '
						</div>';
				}

				wp_reset_postdata();
			}

			if ( 'true' !== $carousel && true !== $carousel ) {
				$output .= '</div>';
			}

			$output .= '</div>';

			return $output;
		}
	}

}

global $lsx_galleries;
$lsx_galleries = new LSX_Galleries();
