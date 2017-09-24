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

		public function get_address_formats() {

		}

		public function create_address_formats() {

		}

		public function get_address_formats_count() {

		}

		public function get_address_formats_by_id() {

		}

		public function delete_address_formats() {

		}

		public function replace_address_formats() {

		}

		public function update_address_formats() {

		}

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
		public function get_companies( $conditions = '', $order_by = '', $child_conditions = '', $custom_field_conditions = '', $page = '', $page_size = '' ) {

			$request = $this->base_uri . '/apis/3.0/company/companies';

			return $this->fetch( $request );

		}

		public function create_company() {

		}

		public function get_companies_count() {

		}

		public function get_company_by_id( $company_id ) {

		}

		public function delete_company() {

		}

		public function replace_company() {

		}

		public function update_company() {

		}

		public function merge_company() {

		}

		public function get_company_contacts( $conditions = '', $order_by  = '', $child_conditions = '', $custom_field_conditions = '', $page  = '', $page_size = '' ) {

			$request = $this->base_uri . '/apis/3.0/company/contacts';

			return $this->fetch( $request );

		}

		public function get_contact_image( $id, $use_default_flag = '', $last_modified = '' ) {

		}

		/* COMPANY - CUSTOM NOTES. */

		public function get_custom_status_notes() {

		}

		public function get_campaigns( $conditions = '', $order_by = '', $child_conditions = '', $customfield_conditions = '', $page = '', $page_size = '' ) {

			$request = $this->base_uri . '/apis/3.0/marketing/campaigns';

			return $this->fetch( $request );

		}

		/* EXPENSE. */

		/* FINANCE. */

		/* MARKETING. */

		/* PROCUREMENT. */

		/* PROJECTS. */

		/**
		 * get_projects function.
		 *
		 * @access public
		 * @param string $conditions (default: '')
		 * @param string $order_by (default: '')
		 * @param string $child_conditions (default: '')
		 * @param string $customfield_conditions (default: '')
		 * @param string $page (default: '')
		 * @param string $page_size (default: '')
		 * @return void
		 */
		public function get_projects( $conditions = '', $order_by = '', $child_conditions = '', $customfield_conditions = '', $page = '', $page_size = '' ) {

			$request = $this->base_uri . '/apis/3.0/project/projects';

			return $this->fetch( $request );

		}

		/* SALES. */

		/* SCHEDULE. */

		/* SERVICE. */


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

		/**
		 * get_tickets_schedule_entries function.
		 *
		 * @access public
		 * @param mixed $ticket_id
		 * @param string $page (default: '')
		 * @param string $page_size (default: '')
		 * @return void
		 */
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

		/**
		 * get_tickets_products function.
		 *
		 * @access public
		 * @param mixed $ticket_id
		 * @param string $page (default: '')
		 * @param string $page_size (default: '')
		 * @return void
		 */
		public function get_tickets_products( $ticket_id, $page = '', $page_size = '' ) {

			$request = $this->base_uri . '/apis/3.0/service/tickets/' . $ticket_id . '/products';

			return $this->fetch( $request );

		}

		/* SYSTEM. */

		// Accounting Packages

		public function get_accounting_packages() {

		}

		public function get_accounting_packages_count( $conditions = '' ) {

		}

		public function get_accounting_packages_by_id( $id ) {

		}


		/* TIME. */


		/**
		 * get_time_entries function.
		 *
		 * @access public
		 * @param mixed $conditions
		 * @param mixed $order_by
		 * @param mixed $child_conditions
		 * @param mixed $custom_field_conditions
		 * @param mixed $page
		 * @param mixed $page_size
		 * @return void
		 */
		public function get_time_entries( $conditions, $order_by, $child_conditions, $custom_field_conditions, $page, $page_size ) {

			$request = $this->base_uri . '/apis/3.0/time/entries';

			return $this->fetch( $request );

		}


		/**
		 * count_time_entries function.
		 *
		 * @access public
		 * @param mixed $conditions
		 * @param mixed $custom_field_conditions
		 * @return void
		 */
		public function count_time_entries( $conditions, $custom_field_conditions ) {

			$request = $this->base_uri . '/apis/3.0/time/entries/count';

			return $this->fetch( $request );

		}

	}
}
