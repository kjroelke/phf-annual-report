<?php
/**
 * Class: CSV Handler
 *
 * @package KJR_Dev
 */

namespace KJR_Dev;

use WP_Error;
/**
 * Handles the CSV file
 */
class CSV_Handler {
	/**
	 * The url to the csv file
	 *
	 * @var string $file_url
	 */
	private string $file_url;

	/**
	 * Whether the list has headers
	 *
	 * @var bool $has_headers
	 */
	private bool $has_headers;

	/**
	 * Inits the ACF fields with a passed post id
	 *
	 * @param int $id The Post Id.
	 */
	public function __construct( int $id ) {
		$this->has_headers = get_field( 'list_has_headers', $id );
		$this->file_url    = $this->has_headers ? get_field( 'donor_list_headers', $id ) : get_field( 'donor_list_no_headers', $id );
	}

	/**
	 * Returns the CSV as an array
	 *
	 * @return array|WP_Error the data, or the WP_Error
	 */
	public function get_the_list(): array|WP_Error {
		$file = $this->read_the_file();
		if ( is_wp_error( $file ) ) {
			return $file;
		}
		return $this->parse_the_file( $file );
	}

	/**
	 * Returns the CSV as a JSON object
	 *
	 * @return array|WP_Error the data, or the WP_Error
	 */
	public function get_the_json_object(): array|WP_Error {
		$list = $this->get_the_list();
		if ( is_wp_error( $list ) ) {
			return $list;
		}
		$data = array_map(
			function ( $name ) {
				$id = esc_html( sanitize_title( $name ) );
				return array(
					'name' => $name,
					'id'   => $id,
				);
			},
			$list
		);
		return $data;
	}

	/**
	 * Reads the file from the set URL and returns its data
	 *
	 * @return string the response body, or a WP_Error
	 */
	private function read_the_file(): string|WP_Error {
		$response = wp_remote_get( $this->file_url );
		if ( is_wp_error( $response ) ) {
			return $response;
		}
		if ( wp_remote_retrieve_response_code( $response ) !== 200 ) {
			return new WP_Error( 'http_error', 'Failed to fetch CSV file.' );
		}
		return wp_remote_retrieve_body( $response );
	}

	/**
	 * Transforms the file from a CSV to an array
	 *
	 * @param string $body the response body
	 * @return array the data
	 */
	private function parse_the_file( string $body ): array {
		$data = array();
		if ( empty( $body ) ) {
			return $data;
		}
		global $wp_filesystem;
		if ( empty( $wp_filesystem ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
			WP_Filesystem();
		}

		// Create a temporary file and write the CSV content to it.
		$temp_file = wp_tempnam();
		$wp_filesystem->put_contents( $temp_file, $body );

		// Open the temporary file for reading.
		$csv_lines = $wp_filesystem->get_contents_array( $temp_file );

		// Read each line into an array.
		foreach ( $csv_lines as $line ) {
			$data[] = trim( $line );
		}

		// Clean up the temporary file.
		$wp_filesystem->delete( $temp_file );

		return $data;
	}
}
