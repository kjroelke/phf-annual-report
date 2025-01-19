<?php
/**
 * Class: CSV Handler
 *
 * @package KJR_Dev
 */

namespace KJR_Dev;

use Exception;
use WP_Error;
/**
 * Handles the CSV file
 */
class CSV_Handler {
	/**
	 * The url(s) to the csv file
	 *
	 * @var string|string[] $file_url
	 */
	private string|array $file_url;

	/**
	 * Whether the file has headers or not, or if the file is a multi-list
	 *
	 * @var 'has_headers'|'no_headers'|'multi_list' $file_type
	 */
	private string $file_type;

	/**
	 * Inits the ACF fields with a passed post id
	 *
	 * @param int $id The Post Id.
	 */
	public function __construct( int $id ) {
		$this->set_file_type();
		$this->set_file_url( $id );
	}

	/**
	 * Sets the file type based on the template
	 */
	private function set_file_type() {
		$file_types      = array(
			'has_headers' => 'templates/donors-list-headers.php',
			'no_headers'  => 'templates/donors-list-no-headers.php',
			'multi_list'  => 'templates/donors-list-multi-list.php',
		);
		$template        = get_page_template_slug( get_the_ID() );
		$this->file_type = array_search( $template, $file_types, true );
	}

	/**
	 * Sets the file URL based on the ACF field and the file type
	 *
	 * @param int $id The Post Id.
	 */
	private function set_file_url( int $id ) {
		$acf_fields = array(
			'has_headers' => 'donor_list_headers',
			'no_headers'  => 'donor_list_no_headers',
			'multi_list'  => 'donor_list_no_headers',
		);
		if ( 'multi_list' === $this->file_type ) {
			if ( have_rows( 'lists', $id ) ) {
				while ( have_rows( 'lists', $id ) ) {
					the_row();
					$this->file_url[ get_sub_field( 'list_label' ) ] = get_sub_field( 'donor_list_no_headers' );
				}
			}
			return;
		}
		$this->file_url = get_field( $acf_fields[ $this->file_type ], $id );
	}

	/**
	 * Returns the CSV as an array
	 *
	 * @return array|WP_Error the data, or the WP_Error
	 * @throws Exception File failed to fetch.
	 */
	public function get_the_list(): array|WP_Error {
		if ( 'multi_list' === $this->file_type ) {
			$data = array();
			foreach ( $this->file_url as $label => $url ) {
				$file = $this->read_the_file( $url );
				if ( is_wp_error( $file ) ) {
					throw new Exception( 'Failed to fetch CSV file.' );
				}
				array_push( $data, array( esc_textarea( ( $label ) ) => $this->parse_the_file( $file ) ) );
			}
			return $data;
		}
		$file = $this->read_the_file();
		if ( is_wp_error( $file ) ) {
			throw new Exception( 'Failed to fetch CSV file.' );
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
		if ( is_array( $list[0] ) ) {
			$data = array();
			foreach ( $list as $donor_list ) {
				$data[ esc_html( sanitize_title( key( $donor_list ) ) ) ] = array_map(
					function ( $name ) {
						$id = esc_html( sanitize_title( $name ) );
						return array(
							'name' => $name,
							'id'   => $id,
						);
					},
					$donor_list[ key( $donor_list ) ]
				);
			}
			return $data;
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
	 * @param string|null $file_url [Optional] the file URL to read.
	 * @return string the response body, or a WP_Error
	 */
	private function read_the_file( ?string $file_url = null ): string|WP_Error {
		$file     = $file_url ?? $this->file_url;
		$response = wp_remote_get( $file );
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
