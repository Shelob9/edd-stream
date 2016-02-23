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
			if( 0 == absint( $atts[ 'id' ] ) ){
				global $post;
				if( is_object( $post ) ){
					$atts[ 'id' ] = $post->ID;
				}
			}

			if( 0 < absint( $atts[ 'id' ] ) ) {
				if ( $atts[ 'restrict' ] && ! edd_has_user_purchased( get_current_user_id(), $atts[ 'id' ] ) ) {
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
						return edd_stream_video( absint( $atts[ 'id' ] ) );
					break;
					case 'audio' :
						return edd_stream_audio( absint( $atts[ 'id' ] ) );
				}

			}

		}

	}

});

