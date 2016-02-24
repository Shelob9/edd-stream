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
	protected function set_html( $files ) {
		if ( ! empty( $files ) ) {
			foreach ( $files as $url ) {
				if ( ! $this->is_s3_url( $url ) ) {
					$this->html .= $this->player( $url );
				} else {
					$this->html .= $this->s3_html( $url );
				}

			}

		}

	}

	/**
	 * Check if video URL is from Amazon s3
	 *
	 * @since 0.1.0
	 *
	 * @param string $url Video URL
	 *
	 * @return bool
	 */
	protected function is_s3_url( $url ){
		if( false !== strpos( $url, 's3.amazonaws' ) ) {
			return true;
		}

	}

	/**
	 * Use a normal HTML5 video player for s3 videos for now
	 *
	 * @TODO This, but better
	 *
	 * @since 0.1.0
	 *
	 * @param string $url Video URL
	 */
	protected function s3_html( $url ){
		if( filter_var( $url, FILTER_VALIDATE_URL ) ) :
	?>
		<video width="640" height="360" controls>
			<source src="<?php echo esc_url_raw( $url ); ?>" type="video/mp4">
		</video>
	<?php
		endif;
	}


}
