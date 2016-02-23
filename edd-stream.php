<?php
/**
 * Plugin Name: EDD Stream
 * Version: 0.1.0
 * Description: Create video or audio players for all files in an EDD download
 * Author: Josh Pollock
 * Author URI: http://JoshPress.net
 * Plugin URI: http://JoshPress.net
 * Text Domain: edd-stream
 * Domain Path: /languages
 * @package Edd-stream
 */

add_action( 'plugins_loaded', function(){
	if( class_exists( 'Easy_Digital_Downloads' ) ){

		add_shortcode( 'edd_stream', 'edd_stream_shortcode_handler' );
		include_once( dirname( __FILE__ ) . '/classes/video.php' );
		include_once( dirname( __FILE__ ) . '/classes/render/videos.php' );

		/**
		 * Output a video player for all videos of a download
		 *
		 * @since 0.1.0
		 *
		 * @param $id
		 *
		 * @return string
		 */
		function edd_stream_video( $id  ){
			$video = new \shelob9\edd_stream\video( $id );
			$files = $video->get_files();
			if( ! empty( $files ) ){
				$render = new \shelob9\edd_stream\render\videos( $files );
				return $render->get_html();

			}
		}

		/**
		 * Output a player for all audio of a download
		 *
		 * @since 0.1.0
		 *
		 * @param $id
		 *
		 * @return string
		 */
		function edd_stream_audio( $id ){
			return;
		}


		/**
		 * Callback for edd_stream shortcode
		 *
		 * @since 0.1.0
		 *
		 * @param array $atts
		 *
		 * @return string|void
		 */
		function edd_stream_shortcode_handler( $atts ){
			$atts = shortcode_atts( array(
				'id'            => 0,
				'type'          => 'video',
				'restrict'      => true,
				'show_login'    => false,
				'login_message' => false
			), $atts, 'edd_stream' );
			$atts[ 'id' ] = absint( $atts[ 'id' ] );
			if( 0 == absint( $atts[ 'id' ] ) ){
				global $post;
				if( is_object( $post ) ){
					$atts[ 'id' ] = $post->ID;
				}
			}

			if( 0 <  $atts[ 'id' ] ) {
				if ( ! current_user_can( 'manage_options' ) && $atts[ 'restrict' ] && ! edd_stream_user_has( $atts[ 'id' ] ) ) {
					if( $atts[ 'show_login' ] ) {
						if( $atts[ 'login_message' ] ) {
							echo sprintf( '<div id="edd-stream-login-message">%s</div>', esc_html( $atts[ 'login_message' ]  ) );
						}

						return edd_login_form();

					}else{

						return;
					}

				}

				switch( $atts[ 'type'] ) {
					case 'video' :
						return edd_stream_video( $atts[ 'id' ] );
					break;
					case 'audio' :
						return edd_stream_audio( $atts[ 'id' ]  );
				}

			}

		}

		/**
		 * Check if current user has purchased a download
		 *
		 * @param int|array $download_id ID or IDs of download(s) to check for
		 *
		 * @since 0.1.0
		 *
		 * @return bool
		 */
		function edd_stream_user_has( $download_id ){
			/**
			 * Filter IDs to check access on.
			 *
			 * @since 0.1.0
			 *
			 * @param int|array $download_id ID or IDs of download(s) to check for
			 */
			$download_id = apply_filters( 'edd_stream_user_has_id', $download_id );

			$has = edd_has_user_purchased( get_current_user_id(), $download_id );

			/**
			 * Filter IDs to check access on.
			 *
			 * @since 0.1.0
			 *
			 * @param bool $has Filter whether or not user has access.
			 */
			return (bool) apply_filters( 'edd_stream_user_has', $has );
		}

	}

});

