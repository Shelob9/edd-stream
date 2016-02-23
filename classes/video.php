<?php
/**
 * Get all the videos from a download
 *
 * @package   edd-stream
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 Josh Pollock
 */

namespace shelob9\edd_stream;


class video {
	/**
	 * The download post object
	 *
	 * @since 0.1.0
	 *
	 * @var \WP_Post
	 */
	private  $download;

	/**
	 * The files from this download
	 *
	 * @since 0.1.0
	 *
	 * @var array
	 */
	private $files;

	/**
	 *
	 * @since 0.1.0
	 *
	 * @param int||WP_Post $id Post ID or object for download
	 */
	public function __construct( $id ){
		$this->set_download( $id );
		if($this->download  ){
			$this->set_files();
		}
	}

	/**
	 * Get all the files from the download
	 *
	 * @since 0.1.0
	 *
	 * @return array
	 */
	public function get_files(){
		return $this->files;
	}

	/**
	 * @param int||WP_Post $id Post ID or object for download
	 */
	private function set_download( $id ){
		if( is_numeric( $id ) ){
			$_download = get_post( $id );
		}else{
			$_download = $id;
		}

		if( is_a( $_download, 'WP_Post' ) ) {
			$this->download = $_download;
		}
	}

	/**
	 * Set the files property of this class
	 *
	 * @since 0.1.0
	 */
	protected function set_files(){
		$this->files = edd_get_download_files( $this->download->ID  );
		if ( ! empty( $this->files ) ) {
			$this->files = wp_list_pluck( $this->files, 'file' );
			foreach( $this->files as $i => $file ){
				if( ! $this->is_video( $file ) ){
					unset( $this->files[ $i ] );
				}

			}

		}

	}

	/**
	 * Check if is acceptable file format
	 *
	 * @since 0.1.0
	 *
	 * @param string $url URL for the video
	 *
	 * @return bool
	 */
	protected function is_video( $url ){
		if( filter_var( $url, FILTER_VALIDATE_URL ) ) {
			$info = new \SplFileInfo( $url );
			return in_array( $info->getExtension(), array( 'mp4', 'ogg' ) );

		}

	}


}
