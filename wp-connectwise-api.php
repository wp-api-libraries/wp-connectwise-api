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
		 * Route being called.
		 *
		 * @var string
		 */
		protected $route = '';

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
		 * ConnectWise Company ID
		 *
		 * @var string
		 */
		static private $company_id;

		/**
		 * ConnectWise Public Key
		 *
		 * @var string
		 */
		static private $public_key;

		/**
		 * ConnectWise Private Key
		 *
		 * @var string
		 */
		static private $private_key;

		/**
		 * ConnectWise links for pagination
		 *
		 * @var string
		 */
		public $links;


		/**
		 * ConnectWise API Version
		 *
		 * @var mixed
		 * @access private
		 * @static
		 */
		static private $api_version;

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
			static::$company_id = $company_id;
			static::$public_key = $public_key;
			static::$private_key = $private_key;

			$this->base_uri = 'https://' . $connectwise_site . '/' . static::$connectwise_version . '/apis/3.0/';
		}

		/**
		 * Prepares API request.
		 *
		 * @param  string $route   API route to make the call to.
		 * @param  array  $args    Arguments to pass into the API call.
		 * @param  array  $method  HTTP Method to use for request.
		 * @return self            Returns an instance of itself so it can be chained to the fetch method.
		 */
		protected function build_request( $route, $args = array(), $method = 'GET' ) {
			// Headers get added first.
			$this->set_headers();

			// Add Method and Route.
			$this->args['method'] = $method;
			$this->route = $route;

			// Generate query string for GET requests.
			if ( 'GET' === $method ) {
				$this->route = add_query_arg( array_filter( $args ), $route );
			}
			// Add to body for all other requests. (Json encode if content-type is json).
			elseif ( 'application/json' === $this->args['headers']['Content-Type'] ) {
				$this->args['body'] = wp_json_encode( $args );
			} else {
				$this->args['body'] = $args;
			}

			return $this;
		}


		/**
		 * Fetch the request from the API.
		 *
		 * @access private
		 * @return array|WP_Error Request results or WP_Error on request failure.
		 */
		protected function fetch() {
			// Make the request.
			$response = wp_remote_request( $this->base_uri . $this->route, $this->args );

			// Retrieve Status code & body.
			$code = wp_remote_retrieve_response_code( $response );
			$body = json_decode( wp_remote_retrieve_body( $response ) );

			$this->set_links($response);

			$this->clear();
			// Return WP_Error if request is not successful.
			if ( ! $this->is_status_ok( $code ) ) {
				return new WP_Error( 'response-error', sprintf( __( 'Status: %d', 'wp-postmark-api' ), $code ), $body );
			}

			return $body;
		}

		protected function set_links( $response ){
		  $this->links = array();

			// Get links from response header.
			$links = wp_remote_retrieve_header( $response, 'link' );

			// Parse the string into a convenient array.
			$links = explode( ',', $links );
			if( ! empty( $links ) ){
				foreach ( $links as $link ) {
					$tmp =  explode( ";", $link );
					$res = preg_match('~<(.*?)>~',$tmp[0], $match );
					if( ! empty( $res ) ){
						// Some string magic to set array key. Changes 'rel="next"' => 'next'.
						$key = str_replace( array( 'rel=', '"' ),'',trim($tmp[1]));
						$this->links[$key] = $match[1];
					}
				}
			}
		}

		/**
		 * Set request headers.
		 */
		protected function set_headers() {
			// Set request headers.
			$this->args['headers'] = array(
				'Content-Type' => 'application/json',
				'Authorization' => 'Basic ' . base64_encode( static::$company_id . '+' . static::$public_key . ':' . static::$private_key ),
			);
		}

		/**
		 * Clear query data.
		 */
		protected function clear() {
			$this->args = array();
		}

		/**
		 * Check if HTTP status code is a success.
		 *
		 * @param  int $code HTTP status code.
		 * @return boolean       True if status is within valid range.
		 */
		protected function is_status_ok( $code ) {
			return ( 200 <= $code && 300 > $code );
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
		public function get_companies( $args = array() ) {
			return $this->build_request( 'company/companies', $args )->fetch();
		}

		public function create_company() {

		}

		public function get_companies_count() {

		}

		public function get_company_by_id( $company_id ) {
			return $this->build_request( "company/companies/$company_id" )->fetch();
		}

		public function delete_company() {

		}

		public function replace_company() {

		}

		public function update_company() {

		}

		public function merge_company() {

		}

		public function get_company_contacts( $args = array() ) {
			return $this->build_request( 'company/contacts', $args )->fetch();
		}

		public function get_company_contacts_by_id( int $contact_id ) {
			return $this->build_request( "company/contacts/$contact_id" )->fetch();
		}

		public function get_contact_image( $id, $use_default_flag = '', $last_modified = '' ) {

		}

		/* COMPANY - CUSTOM NOTES. */

		public function get_custom_status_notes() {

		}

		public function get_campaigns( $args = array() ) {
			return $this->build_request( 'marketing/campaigns', $args )->fetch();
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
		public function get_projects( $args = array() ) {
			return $this->build_request( 'project/projects', $args )->fetch();
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
		public function get_tickets( $args = array() ) {
			return $this->build_request( 'service/tickets', $args )->fetch();
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
		public function get_ticket_activities( $ticket_id, $args = array() ) {
			return $this->build_request( "service/tickets/$ticket_id/activities", $args )->fetch();
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
		public function get_tickets_time_entries( $ticket_id, $args = array() ) {
			return $this->build_request( "service/tickets/$ticket_id/timeentries", $args )->fetch();
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
		public function get_tickets_schedule_entries( $ticket_id, $args = array() ) {
			return $this->build_request( "service/tickets/$ticket_id/scheduleentries", $args )->fetch();
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
		public function get_tickets_notes( $ticket_id, $args = array() ) {
			return $this->build_request( "service/tickets/$ticket_id/notes", $args )->fetch();
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
		public function get_tickets_products( $ticket_id, $args = array() ) {
			return $this->build_request( "service/tickets/$ticket_id/products", $args )->fetch();
		}

		/* EXPENSE. */

		/* EXPENSE. */

		/* FINANCE. */

		/* MARKETING. */

		/* PROCUREMENT. */

		/* PROJECT. */

		/* SALES. */

		/* SCHEDULE. */


		/* SERVICE. */


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
		public function get_time_entries( $args = array() ) {
			return $this->build_request( "time/entries", $args )->fetch();
		}


		/**
		 * count_time_entries function.
		 *
		 * @access public
		 * @param mixed $conditions
		 * @param mixed $custom_field_conditions
		 * @return void
		 */
		public function count_time_entries( $args = array() ) {
			return $this->build_request( "time/entries/count", $args )->fetch();
		}

	}
}
