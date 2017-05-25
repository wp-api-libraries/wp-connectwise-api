<?php
/**
 * WP-ConnectWise-API (https://developer.connectwise.com/manage/rest)
 *
 * @package WP-ConnectWise-API
 */

/*
* Plugin Name: WP ConnectWise API
* Plugin URI: https://github.com/wp-api-libraries/wp-connectwise-api
* Description: Perform API requests to ConnectWise in WordPress.
* Author: WP API Libraries
* Version: 1.0.0
* Author URI: https://wp-api-libraries.com
* GitHub Plugin URI: https://github.com/wp-api-libraries/wp-connectwise-api
* GitHub Branch: master
*/

/* Exit if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) { exit; }


/* Check if class exists. */
if ( ! class_exists( 'ConnectWiseAPI' ) ) {

	/**
	 * ConnectWise API Class.
	 */
	class ConnectWiseAPI {

		/**
		 * ConnectWise Site
		 *
		 * @var string
		 */
		static private $connectwise_site;

		/**
		 * ConnectWise Version
		 *
		 * @var string
		 */
		static private $connectwise_version;

		/**
		 * BaseAPI Endpoint
		 *
		 * @var string
		 * @access protected
		 */
		protected $base_uri;


		/**
		 * Construct.
		 *
		 * @access public
		 * @param mixed $api_key API Key.
		 * @param mixed $connectwise_site ConnectWise Site URL.
		 * @return void
		 */
		public function __construct( $connectwise_site, $connectwise_version, $company_id, $public_key, $private_key ) {

			static::$connectwise_site = $connectwise_site;
			static::$connectwise_version = $connectwise_version;

			$this->base_uri = 'https://' . $connectwise_site . '/' . static::$connectwise_version;

			$this->args['headers'] = array(
				'Content-Type' => 'application/json',
				'Authorization' => 'Basic ' . base64_encode($company_id.'+'.$public_key.':'.$private_key),
			);

		}

		/**
		 * Fetch the request from the API.
		 *
		 * @access private
		 * @param mixed $request Request URL.
		 * @return $body Body.
		 */
		private function fetch( $request ) {

			$response = wp_remote_get( $request, $this->args );
			$code = wp_remote_retrieve_response_code( $response );

			if ( 200 !== $code ) {
				return new WP_Error( 'response-error', sprintf( __( 'Server response code: %d', 'wp-connectwise-api' ), $code ) );
			}

			$body = wp_remote_retrieve_body( $response );

			return json_decode( $body );

		}

		/* COMPANIES. */

		/**
		 * Get Companies.
		 *
		 * @access public
		 * @param mixed $conditions Conditions.
		 * @param mixed $order_by Order By.
		 * @param mixed $child_conditions Child Conditions.
		 * @param mixed $custom_field_conditions Custom Field Conditions.
		 * @param mixed $page Page.
		 * @param mixed $page_size Page Size.
		 * @return void
		 */
		public function get_companies( $conditions, $order_by, $child_conditions, $custom_field_conditions, $page, $page_size ) {

			$request = $this->base_uri . '/apis/3.0/company/companies';

			return $this->fetch( $request );

		}

		public function add_company() {

		}

		public function get_companies_count() {

		}

		public function get_company( $company_id ) {

		}

		public function delete_company() {

		}

		public function replace_company() {

		}

		public function update_company() {

		}

		public function merge_company() {

		}

		/* COMPANY - CUSTOM NOTES. */

		public function get_custom_status_notes() {

		}

		/**
		 * Get Tickets.
		 *
		 * @access public
		 * @return void
		 */
		public function get_tickets() {

			$request = $this->base_uri . '/apis/3.0/service/tickets';

			return $this->fetch( $request );

		}

		/**
		 * get_tickets_activities function.
		 *
		 * @access public
		 * @param mixed $ticket_id
		 * @param string $page (default: '')
		 * @param string $page_size (default: '')
		 * @return void
		 */
		public function get_tickets_activities( $ticket_id, $page = '', $page_size = '' ) {

			$request = $this->base_uri . '/apis/3.0/service/tickets/' . $ticket_id . '/activities';

			return $this->fetch( $request );

		}

		/**
		 * get_tickets_time_entries function.
		 *
		 * @access public
		 * @param mixed $ticket_id
		 * @param string $page (default: '')
		 * @param string $page_size (default: '')
		 * @return void
		 */
		public function get_tickets_time_entries( $ticket_id, $page = '', $page_size = '' ) {

			$request = $this->base_uri . '/apis/3.0/service/tickets/' . $ticket_id . '/timeentries';

			return $this->fetch( $request );

		}

		public function get_tickets_schedule_entries( $ticket_id, $page = '', $page_size = '' ) {

			$request = $this->base_uri . '/apis/3.0/service/tickets/' . $ticket_id . '/scheduleentries';

			return $this->fetch( $request );

		}

		/**
		 * get_tickets_notes function.
		 *
		 * @access public
		 * @param mixed $id
		 * @param string $conditions (default: '')
		 * @param string $order_by (default: '')
		 * @param string $child_conditions (default: '')
		 * @param string $custom_field_conditions (default: '')
		 * @param string $page (default: '')
		 * @param string $page_size (default: '')
		 * @return void
		 */
		public function get_tickets_notes( $ticket_id, $conditions ='', $order_by = '', $child_conditions = '', $custom_field_conditions = '', $page = '', $page_size = '' ) {

			$request = $this->base_uri . '/apis/3.0/service/tickets/' . $ticket_id . '/notes';

			return $this->fetch( $request );

		}


	}
}
