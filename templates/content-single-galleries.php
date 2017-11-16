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

	//$youtube_url = get_post_meta( get_the_ID(), 'lsx_gallery_youtube', true );
	$gallery_id = get_post_meta( get_the_ID(), 'lsx_gallery_gallery', true );
	$views = (int) get_post_meta( get_the_ID(), '_views', true );

	if ( ! empty( $gallery_id ) ) {
		$gallery_url = wp_get_attachment_url( $gallery_id );
		$gallery_meta = get_post_meta( $gallery_id , '_wp_attachment_metadata', true );
	}

?>

<div class="<?php echo esc_attr( apply_filters( 'lsx_slot_class', 'col-md-12' ) ); ?> lsx-galleries-column <?php echo esc_attr( $categories_class ); ?>">
	<article>
		<div class="lsx-full-width-base-small">
			<div class="row">
				<div class="col-md-4">
					<a href="#lsx-galleries-modal" data-toggle="modal" data-post-id="<?php the_ID(); ?>" data-gallery="<?php echo esc_url( $gallery_url ); ?>" data-title="<?php the_title(); ?>">
						<figure class="lsx-galleries-avatar"><?php lsx_thumbnail( 'lsx-galleries-cover' ); ?></figure>
				</a>
				</div>
				<div class="col-md-8">
					<h5 class="lsx-galleries-title"><?php the_title(); ?></h5>
					<?php if ( ! empty( $categories ) ) : ?>
						<p class="lsx-galleries-categories"><?php echo wp_kses_post( $categories ); ?></p>
					<?php endif; ?>
				<p class="lsx-galleries-meta"><?php echo wp_kses_post( $meta ); ?></p>
				<div class="lsx-galleries-content"><?php the_content(); ?></div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-3">
				<?php $img_1_figure = get_post_meta( get_the_ID(), 'lsx_gallery_image_01_file', true );
					if ( ! empty( $img_1_figure ) ) {
						echo '<img src="' .wp_get_attachment_url( $img_1_figure ). '">';
					}

				?>
				<?php $img_1_title = get_post_meta( get_the_ID(), 'lsx_gallery_image_01_title', true );
					if ( ! empty( $img_1_title ) ) {
						echo '<p>Image title: '.$img_1_title.'</p>';
					}
				?>
				<?php $img_1_description = get_post_meta( get_the_ID(), 'lsx_gallery_image_01_description', true );
					if ( ! empty( $img_1_description ) ) {
						echo '<p>Image description: '.$img_1_description.'</p>';
					}
				?>
			</div>
			<div class="col-md-3">
				<?php $img_2_figure = get_post_meta( get_the_ID(), 'lsx_gallery_image_02_file', true );
					if ( ! empty( $img_2_figure ) ) {
						echo '<img src="' .wp_get_attachment_url( $img_2_figure ). '">';
					}

				?>
				<?php $img_2_title = get_post_meta( get_the_ID(), 'lsx_gallery_image_02_title', true );
					if ( ! empty( $img_2_title ) ) {
						echo '<p>Image title: '.$img_2_title.'</p>';
					}
				?>
				<?php $img_2_description = get_post_meta( get_the_ID(), 'lsx_gallery_image_02_description', true );
					if ( ! empty( $img_2_description ) ) {
						echo '<p>Image description: '.$img_2_description.'</p>';
					}
				?>
			</div>
			<div class="col-md-3">
				<?php $img_3_figure = get_post_meta( get_the_ID(), 'lsx_gallery_image_03_file', true );
					if ( ! empty( $img_3_figure ) ) {
						echo '<img src="' .wp_get_attachment_url( $img_3_figure ). '">';
					}

				?>
				<?php $img_3_title = get_post_meta( get_the_ID(), 'lsx_gallery_image_03_title', true );
					if ( ! empty( $img_3_title ) ) {
						echo '<p>Image title: '.$img_3_title.'</p>';
					}
				?>
				<?php $img_3_description = get_post_meta( get_the_ID(), 'lsx_gallery_image_03_description', true );
					if ( ! empty( $img_3_description ) ) {
						echo '<p>Image description: '.$img_3_description.'</p>';
					}
				?>
			</div>
			<div class="col-md-3">
				<?php $img_4_figure = get_post_meta( get_the_ID(), 'lsx_gallery_image_04_file', true );
					if ( ! empty( $img_4_figure ) ) {
						echo '<img src="' .wp_get_attachment_url( $img_4_figure ). '">';
					}

				?>
				<?php $img_4_title = get_post_meta( get_the_ID(), 'lsx_gallery_image_04_title', true );
					if ( ! empty( $img_4_title ) ) {
						echo '<p>Image title: '.$img_4_title.'</p>';
					}
				?>
				<?php $img_4_description = get_post_meta( get_the_ID(), 'lsx_gallery_image_04_description', true );
					if ( ! empty( $img_4_description ) ) {
						echo '<p>Image description: '.$img_4_description.'</p>';
					}
				?>
			</div>
			<div class="col-md-3">
				<?php $img_5_figure = get_post_meta( get_the_ID(), 'lsx_gallery_image_05_file', true );
					if ( ! empty( $img_5_figure ) ) {
						echo '<img src="' .wp_get_attachment_url( $img_5_figure ). '">';
					}

				?>
				<?php $img_5_title = get_post_meta( get_the_ID(), 'lsx_gallery_image_05_title', true );
					if ( ! empty( $img_5_title ) ) {
						echo '<p>Image title: '.$img_5_title.'</p>';
					}
				?>
				<?php $img_5_description = get_post_meta( get_the_ID(), 'lsx_gallery_image_05_description', true );
					if ( ! empty( $img_5_description ) ) {
						echo '<p>Image description: '.$img_5_description.'</p>';
					}
				?>
			</div>
			<div class="col-md-3">
				<?php $img_6_figure = get_post_meta( get_the_ID(), 'lsx_gallery_image_06_file', true );
					if ( ! empty( $img_6_figure ) ) {
						echo '<img src="' .wp_get_attachment_url( $img_6_figure ). '">';
					}

				?>
				<?php $img_6_title = get_post_meta( get_the_ID(), 'lsx_gallery_image_06_title', true );
					if ( ! empty( $img_6_title ) ) {
						echo '<p>Image title: '.$img_6_title.'</p>';
					}
				?>
				<?php $img_6_description = get_post_meta( get_the_ID(), 'lsx_gallery_image_06_description', true );
					if ( ! empty( $img_6_description ) ) {
						echo '<p>Image description: '.$img_6_description.'</p>';
					}
				?>
			</div>
			<div class="col-md-3">
				<?php $img_7_figure = get_post_meta( get_the_ID(), 'lsx_gallery_image_07_file', true );
					if ( ! empty( $img_7_figure ) ) {
						echo '<img src="' .wp_get_attachment_url( $img_7_figure ). '">';
					}

				?>
				<?php $img_7_title = get_post_meta( get_the_ID(), 'lsx_gallery_image_07_title', true );
					if ( ! empty( $img_7_title ) ) {
						echo '<p>Image title: '.$img_7_title.'</p>';
					}
				?>
				<?php $img_7_description = get_post_meta( get_the_ID(), 'lsx_gallery_image_07_description', true );
					if ( ! empty( $img_7_description ) ) {
						echo '<p>Image description: '.$img_7_description.'</p>';
					}
				?>
			</div>
			<div class="col-md-3">
				<?php $img_8_figure = get_post_meta( get_the_ID(), 'lsx_gallery_image_08_file', true );
					if ( ! empty( $img_8_figure ) ) {
						echo '<img src="' .wp_get_attachment_url( $img_8_figure ). '">';
					}

				?>
				<?php $img_8_title = get_post_meta( get_the_ID(), 'lsx_gallery_image_08_title', true );
					if ( ! empty( $img_8_title ) ) {
						echo '<p>Image title: '.$img_8_title.'</p>';
					}
				?>
				<?php $img_8_description = get_post_meta( get_the_ID(), 'lsx_gallery_image_08_description', true );
					if ( ! empty( $img_8_description ) ) {
						echo '<p>Image description: '.$img_8_description.'</p>';
					}
				?>
			</div>
			<div class="col-md-3">
				<?php $img_9_figure = get_post_meta( get_the_ID(), 'lsx_gallery_image_09_file', true );
					if ( ! empty( $img_9_figure ) ) {
						echo '<img src="' .wp_get_attachment_url( $img_9_figure ). '">';
					}

				?>
				<?php $img_9_title = get_post_meta( get_the_ID(), 'lsx_gallery_image_09_title', true );
					if ( ! empty( $img_9_title ) ) {
						echo '<p>Image title: '.$img_9_title.'</p>';
					}
				?>
				<?php $img_9_description = get_post_meta( get_the_ID(), 'lsx_gallery_image_09_description', true );
					if ( ! empty( $img_9_description ) ) {
						echo '<p>Image description: '.$img_9_description.'</p>';
					}
				?>
			</div>
			<div class="col-md-3">
				<?php $img_10_figure = get_post_meta( get_the_ID(), 'lsx_gallery_image_10_file', true );
					if ( ! empty( $img_10_figure ) ) {
						echo '<img src="' .wp_get_attachment_url( $img_10_figure ). '">';
					}

				?>
				<?php $img_10_title = get_post_meta( get_the_ID(), 'lsx_gallery_image_10_title', true );
					if ( ! empty( $img_10_title ) ) {
						echo '<p>Image title: '.$img_10_title.'</p>';
					}
				?>
				<?php $img_10_description = get_post_meta( get_the_ID(), 'lsx_gallery_image_10_description', true );
					if ( ! empty( $img_10_description ) ) {
						echo '<p>Image description: '.$img_10_description.'</p>';
					}
				?>
			</div>
		</div>
	</article>
</div>
