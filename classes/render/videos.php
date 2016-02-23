<?php
/**
 * Render an array of video files
 *
 * @package   edd_stream
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 Josh Pollock
 */

namespace shelob9\edd_stream\render;


class videos {

	/**
	 * Rendered HTML
	 *
	 * @since 0.1,0
	 *
	 * @var string
	 */
	private  $html = '';


	/**
	 * Construct object to make HTML
	 *
	 * @since 0.1.0
	 *
	 * @param array $files Array of files.
	 */
	public function __construct( $files ){
		$this->set_html( $files );
	}

	/**
	 * Get rendered HTML
	 *
	 * @since 0.1.0
	 *
	 * @return string
	 */
	public function get_html(){
		return $this->html;
	}

	/**
	 * Get atts for wp_video_shortcode()
	 *
	 * @since 0.1.0
	 *
	 * @return array
	 */
	protected function atts() {
		return wp_parse_args(
			apply_filters( 'edd_stream_video_player_args', array() ),
			array(
				'poster'   => '',
				'loop'     => '',
				'autoplay' => '',
				'preload'  => 'metadata',
				'width'    => 640,
				'height'   => 360,
			)
		);

	}

	/**
	 * Render player for one video
	 *
	 * @since 0.1.0
	 *
	 * @param string $url URL for the video
	 *
	 * @return string|void
	 */
	protected function player( $url ){
		if( ! filter_var( $url, FILTER_VALIDATE_URL ) ) {
			return;
		}

		$atts = $this->atts();
		$atts[ 'src' ] = $url;

		return wp_video_shortcode( $atts );

	}

	/**
	 * Set up the HTML for all videos and put in HTML property
	 *
	 * @since 0.1.0
	 *
	 * @param array $files The files
	 */
	private function set_html( $files ) {
		if ( ! empty( $files ) ) {
			foreach ( $files as $url ) {
				$this->html .= $this->player( $url );

			}

		}

	}



}
