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

			foreach( $this->files as $i => $file ) {

				if( ! $this->is_video( $file[ 'file' ] ) ){
					unset( $this->files[ $i ] );
				}

				if( class_exists( 'EDD_Amazon_S3' ) ) {
					if( $this->is_s3( $i ) ) {
						$this->files[ $i ][ 'file' ] = \EDD_Amazon_S3::get_instance()->get_s3_url( $file[ 'file' ], 60 );
					}
				}

			}

			$this->files = wp_list_pluck( $this->files, 'file' );

		}

	}

	/**
	 * Check if a video file is from Amazon s3
	 *
	 * @since 0.1.0
	 *
	 * @param $file_id
	 *
	 * @return bool
	 */
	protected function is_s3( $file_id ) {
		if( isset( $this->files[ $file_id ] ) ) {

			$file_name = $this->files[ $file_id ]['file'];
			// Check whether thsi is an Amazon S3 file or not
			if( ( '/' !== $file_name[0] && strpos( $file_name, 'http://' ) === false && strpos( $file_name, 'https://' ) === false && strpos( $file_name, 'ftp://' ) === false ) || false !== ( strpos( $file_name, 'AWSAccessKeyId' ) ) ) {

				return true;

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

			$info = new \SplFileInfo( $url );

			return in_array( $info->getExtension(), array( 'mp4', 'ogg' ) );



	}


}
