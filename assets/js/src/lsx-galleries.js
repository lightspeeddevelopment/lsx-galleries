/**
 * LSX Galleries scripts.
 *
 * @package lsx-galleries
 * @subpackage scripts
 */

var lsx_galleries = Object.create( null );

;( function( $, window, document, undefined ) {

	'use strict';

	lsx_galleries.document = $( document );
	lsx_galleries.window = $( window );
	lsx_galleries.window_height = window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight;
	lsx_galleries.window_width = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;

	/**
	 * Init galleries widget/shotcode slider.
	 *
	 * @package    lsx-galleries
	 * @subpackage scripts
	 */
	lsx_galleries.init_slider = function() {
		$( '.lsx-galleries-slider' ).each( function( index, el ) {
			var $gallery_slider = $( this );

			$gallery_slider.on( 'init', function( event, slick ) {
				if ( slick.options.arrows && slick.slideCount > slick.options.slidesToShow ) {
					$gallery_slider.addClass( 'slick-has-arrows' );
				}
			} );

			$gallery_slider.on( 'setPosition', function( event, slick ) {
				if ( ! slick.options.arrows ) {
					$gallery_slider.removeClass( 'slick-has-arrows' );
				} else if ( slick.slideCount > slick.options.slidesToShow ) {
					$gallery_slider.addClass( 'slick-has-arrows' );
				}
			} );

			$gallery_slider.slick( {
				draggable: false,
				infinite: true,
				swipe: false,
				cssEase: 'ease-out',
				dots: true,
				responsive: [{
					breakpoint: 992,
					settings: {
						slidesToShow: 3,
						slidesToScroll: 3,
						draggable: true,
						arrows: false,
						swipe: true
					}
				}, {
					breakpoint: 768,
					settings: {
						slidesToShow: 1,
						slidesToScroll: 1,
						draggable: true,
						arrows: false,
						swipe: true
					}
				}]
			} );
		} );
	};

	/**
	 * Adds modal effect to open single galleries.
	 *
	 * @package    lsx-galleries
	 * @subpackage scripts
	 */
	lsx_galleries.init_modal = function() {
		$( '#lsx-galleries-modal' ).on( 'show.bs.modal', function( event ) {
			var $modal = $( this ),
				$invoker = $( event.relatedTarget );

			$modal.find( '.modal-title' ).html( $invoker.data( 'title' ) );
			$modal.find( '.modal-body' ).html( '<div class="alert alert-info">Loading...</div>' );
		} );

		$( '#lsx-galleries-modal' ).on( 'shown.bs.modal', function( event ) {
			var $modal = $( this ),
				$invoker = $( event.relatedTarget );

			$.ajax( {
				url: lsx_galleries_params.ajax_url,

				data: {
					action: 'get_gallery_embed',
					gallery: $invoker.data( 'gallery' ),
					post_id: $invoker.data( 'post-id' )
				},

				success: function( data, textStatus, jqXHR ) {
					$modal.find( '.modal-body' ).html( data );
				},

				error: function( textStatus, jqXHR, errorThrown ) {
					$modal.find( '.modal-body' ).html( '<div class="alert alert-danger">Error!</div>' );
				}
			} );
		} );

		$( '#lsx-galleries-modal' ).on( 'hidden.bs.modal', function( event ) {
			var $modal = $( this );

			$modal.find( '.modal-title' ).html( '' );
			$modal.find( '.modal-body' ).html( '' );
		} );
	};

	/**
	 * On document ready.
	 *
	 * @package lsx-galleries
	 * @subpackage scripts
	 */
	lsx_galleries.document.ready( function() {
		lsx_galleries.init_slider();
		lsx_galleries.init_modal();
	} );

} )( jQuery, window, document );
