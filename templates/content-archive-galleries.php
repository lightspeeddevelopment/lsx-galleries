<?php
/**
 * @package lsx-galleries
 */
?>

<?php
	global $lsx_galleries_frontend;

	$categories = '';
	$categories_class = '';
	$terms = get_the_terms( get_the_ID(), 'gallery-category' );

	if ( $terms && ! is_wp_error( $terms ) ) {
		$categories = array();
		$categories_class = array();

		foreach ( $terms as $term ) {
			$categories[] = '<a href="' . get_term_link( $term ) . '">' . $term->name . '</a>';
			$categories_class[] = 'filter-' . $term->slug;
		}

		$categories = join( ', ', $categories );
		$categories_class = join( ' ', $categories_class );
	}

	$youtube_url = get_post_meta( get_the_ID(), 'lsx_gallery_youtube', true );
	$gallery_id = get_post_meta( get_the_ID(), 'lsx_gallery_gallery', true );
	$views = (int) get_post_meta( get_the_ID(), '_views', true );

	if ( ! empty( $gallery_id ) ) {
		$gallery_url = wp_get_attachment_url( $gallery_id );
		$gallery_meta = get_post_meta( $gallery_id , '_wp_attachment_metadata', true );
	}

	if ( ! empty( $youtube_url ) ) {
		$gallery_url = $youtube_url;
	}

	// if ( 1 !== $views ) {
	//	/* Translators: 1: gallery views */
	//	$meta = sprintf( esc_html__( '%1$s views', 'lsx-galleries' ), $views );
	// } else {
	//	$meta = esc_html__( '1 view', 'lsx-galleries' );
	// }

	// if ( ! empty( $gallery_meta ) && ! empty( $gallery_meta['length_formatted'] ) ) {
	//	$length = $gallery_meta['length_formatted'];
	//	$meta = $length . ' | ' . $meta;
	// }

	/* Translators: 1: time ago (gallery published date) */
	// $meta .= ' | ' . sprintf( esc_html__( '%1$s ago', 'lsx-galleries' ), human_time_diff( get_the_time( 'U' ), current_time( 'timestamp' ) ) );
?>

<div class="<?php echo esc_attr( apply_filters( 'lsx_slot_class', 'col-xs-12 col-sm-4 col-md-3' ) ); ?> lsx-galleries-column <?php echo esc_attr( $categories_class ); ?>">
	<article class="lsx-galleries-slot">
		<a href="<?php echo get_page_link( $the_ID ); ?>" data-toggle="modal" data-post-id="<?php the_ID(); ?>" data-gallery="<?php echo esc_url( $gallery_url ); ?>" data-title="<?php the_title(); ?>">
			<figure class="lsx-galleries-avatar"><?php lsx_thumbnail( 'lsx-galleries-cover' ); ?></figure>
		</a>

		<h5 class="lsx-galleries-title">
			<a href="<?php echo get_page_link( $the_ID ); ?>" data-toggle="modal" data-post-id="<?php the_ID(); ?>" data-gallery="<?php echo esc_url( $gallery_url ); ?>" data-title="<?php the_title(); ?>"><?php the_title(); ?></a>
		</h5>

		<?php if ( ! empty( $categories ) ) : ?>
			<p class="lsx-galleries-categories"><?php echo wp_kses_post( $categories ); ?></p>
		<?php endif; ?>

		<p class="lsx-galleries-meta"><?php echo wp_kses_post( $meta ); ?></p>

		<?php if ( empty( $lsx_galleries_frontend->options['display'] ) || empty( $lsx_galleries_frontend->options['display']['galleries_disable_excerpt'] ) ) : ?>
			<div class="lsx-galleries-content"><?php the_excerpt(); ?></div>
		<?php else : ?>
			<div class="lsx-galleries-content"><a href="#lsx-galleries-modal" data-toggle="modal" data-post-id="<?php the_ID(); ?>" data-gallery="<?php echo esc_url( $gallery_url ); ?>" data-title="<?php the_title(); ?>" class="moretag"><?php esc_html_e( 'View gallery', 'lsx-galleries' ); ?></a></div>
		<?php endif; ?>
	</article>
</div>
