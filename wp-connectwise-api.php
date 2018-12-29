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
if ( ! defined( 'ABSPATH' ) ) {
	exit; }


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

			static::$connectwise_site    = $connectwise_site;
			static::$connectwise_version = $connectwise_version;
			static::$company_id          = $company_id;
			static::$public_key          = $public_key;
			static::$private_key         = $private_key;

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
			$this->route          = $route;

			// Generate query string for GET requests.
			if ( 'GET' === $method ) {
				$this->route = add_query_arg( array_filter( $args ), $route );
			} // Add to body for all other requests. (Json encode if content-type is json).
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

			$this->set_links( $response );

			$this->clear();
			// Return WP_Error if request is not successful.
			if ( ! $this->is_status_ok( $code ) ) {
				return new WP_Error( 'response-error', sprintf( __( 'Status: %d', 'wp-postmark-api' ), $code ), $body );
			}

			return $body;
		}

		/**
		 * set_links function.
		 *
		 * @access protected
		 * @param mixed $response
		 * @return void
		 */
		protected function set_links( $response ) {
			$this->links = array();

			// Get links from response header.
			$links = wp_remote_retrieve_header( $response, 'link' );

			// Parse the string into a convenient array.
			$links = explode( ',', $links );
			if ( ! empty( $links ) ) {
				foreach ( $links as $link ) {
					$tmp = explode( ';', $link );
					$res = preg_match( '~<(.*?)>~', $tmp[0], $match );
					if ( ! empty( $res ) ) {
						// Some string magic to set array key. Changes 'rel="next"' => 'next'.
						$key                 = str_replace( array( 'rel=', '"' ), '', trim( $tmp[1] ) );
						$this->links[ $key ] = $match[1];
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
				'Content-Type'  => 'application/json',
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

		/*
		 =========================================================== COMPANY. =========================================================== */
		/* Company Docs - https://developer.connectwise.com/manage/rest?a=Company */

		/* Address Formats. */

		/**
		 * get_address_formats function.
		 *
		 * @access public
		 * @param array $args (default: array())
		 * @return void
		 */
		public function get_address_formats( $args = array() ) {
			return $this->build_request( 'company/addressFormats', $args )->fetch();
		}

		/**
		 * create_address_formats function.
		 *
		 * @access public
		 * @return void
		 */
		public function create_address_formats() {
			return $this->build_request( 'company/addressFormats', $args, 'POST' )->fetch();
		}

		/**
		 * get_address_formats_count function.
		 *
		 * @access public
		 * @param array $args (default: array())
		 * @return void
		 */
		public function get_address_formats_count( $args = array() ) {
			return $this->build_request( '/addressFormats/count', $args )->fetch();
		}

		/**
		 * get_address_formats_by_id function.
		 *
		 * @access public
		 * @param mixed $address_format_id
		 * @param array $args (default: array())
		 * @return void
		 */
		public function get_address_formats_by_id( $address_format_id, $args = array() ) {
			return $this->build_request( "/addressFormats/$address_format_id", $args )->fetch();
		}

		/**
		 * delete_address_formats function.
		 *
		 * @access public
		 * @param mixed $address_format_id
		 * @return void
		 */
		public function delete_address_formats( $address_format_id ) {
			return $this->build_request( "company/addressFormats/$address_format_id", null, 'DELETE' )->fetch();
		}

		/**
		 * replace_address_formats function.
		 *
		 * @access public
		 * @param mixed $address_format_id
		 * @return void
		 */
		public function replace_address_formats( $address_format_id ) {
			return $this->build_request( "company/addressFormats/$address_format_id", null, 'PUT' )->fetch();
		}

		/**
		 * update_address_formats function.
		 *
		 * @access public
		 * @param mixed $address_format_id
		 * @return void
		 */
		public function update_address_formats( $address_format_id ) {
			return $this->build_request( "company/addressFormats/$address_format_id", null, 'PATCH' )->fetch();
		}

		/* Companies. */

		/**
		 * Get Companies.
		 *
		 * @access public
		 * @docs https://developer.connectwise.com/manage/rest?a=Company&e=Companies&o=GET
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

		/**
		 * Create Company
		 *
		 * @docs https://developer.connectwise.com/manage/rest?a=Company&e=Companies&o=CREATE
		 *
		 * @access public
		 * @param array $args (default: array())
		 * @return void
		 */
		public function create_company( $args = array() ) {
			return $this->build_request( 'company/companies', $args )->fetch();
		}

		/**
		 * Get Company Counts.
		 *
		 * @access public
		 * @param mixed $conditions Conditions.
		 * @param mixed $custom_field_conditions Custom Field Conditions.
		 * @param array $args (default: array())
		 * @return void
		 */
		public function get_companies_count( $args = array() ) {
			return $this->build_request( 'company/companies/count', $args )->fetch();
		}

		/**
		 * get_company_by_id function.
		 *
		 * @access public
		 * @param mixed $company_id
		 * @return void
		 */
		public function get_company_by_id( $company_id ) {
			return $this->build_request( "company/companies/$company_id" )->fetch();
		}

		/**
		 * delete_company function.
		 *
		 * @access public
		 * @param mixed $company_id
		 * @return void
		 */
		public function delete_company( $company_id ) {
			return $this->build_request( "company/companies/$company_id", null, 'DELETE' )->fetch();
		}

		/**
		 * replace_company function.
		 *
		 * @access public
		 * @param mixed $company_id
		 * @param array $args (default: array())
		 * @return void
		 */
		public function replace_company( $company_id, $args = array() ) {
			return $this->build_request( "company/companies/$company_id", $args, 'PUT' )->fetch();
		}

		/**
		 * update_company function.
		 *
		 * @access public
		 * @param mixed $company_id
		 * @param array $args (default: array())
		 * @return void
		 */
		public function update_company( $company_id, $args = array() ) {
			return $this->build_request( "company/companies/$company_id", $args, 'PATCH' )->fetch();
		}

		/**
		 * merge_company function.
		 *
		 * @access public
		 * @param mixed $company_id
		 * @param array $args (default: array())
		 * @return void
		 */
		public function merge_company( $company_id, $args = array() ) {
			return $this->build_request( "company/companies/$company_id", $args, 'POST' )->fetch();
		}

		/* Company Custom Notes. */

		public function get_company_custom_status_notes() {

		}

		public function create_company_custom_status_notes() {

		}

		public function get_company_custom_status_notes_count() {

		}

		public function get_company_custom_status_notes_by_id() {

		}

		public function delete_company_custom_status_notes() {

		}

		public function replace_company_custom_status_notes() {

		}

		public function update_company_custom_status_notes() {

		}

		/* Company Groups. */

		/* Company Management Summary Reports. */

		/* Company Note Types. */

		/* Company Notes. */

		/* Company Picker Items. */

		/* Company Sites. */

		/**
		 * get_company_sites function.
		 *
		 * @access public
		 * @param mixed $company_id
		 * @param array $args (default: array())
		 * @return void
		 */
		public function get_company_sites( $company_id, $args = array() ) {
			return $this->build_request( "company/companies/$company_id/sites" )->fetch();
		}

		/**
		 * get_company_sites_count function.
		 *
		 * @access public
		 * @param mixed $company_id
		 * @param array $args (default: array())
		 * @return void
		 */
		public function get_company_sites_count( $company_id, $args = array() ) {
			return $this->build_request( "company/companies/$company_id/sites/count" )->fetch();
		}

		/**
		 * get_company_site_by_id function.
		 *
		 * @access public
		 * @param mixed $company_id
		 * @param array $args (default: array())
		 * @return void
		 */
		public function get_company_site_by_id( $company_id, $args = array() ) {
			return $this->build_request( "company/companies/$company_id/sites/$site_id" )->fetch();
		}

		/* Company Contacts. */

		/**
		 * get_company_contacts function.
		 *
		 * @access public
		 * @param array $args (default: array())
		 * @return void
		 */
		public function get_company_contacts( $args = array() ) {
			return $this->build_request( 'company/contacts', $args )->fetch();
		}

		/**
		 * create_company_contact function.
		 *
		 * @access public
		 * @param array $args (default: array())
		 * @return void
		 */
		public function create_company_contact( $args = array() ) {
			return $this->build_request( 'company/contacts', $args, 'POST' )->fetch();
		}

		/**
		 * get_company_contacts_count function.
		 *
		 * @access public
		 * @param array $args (default: array())
		 * @return void
		 */
		public function get_company_contacts_count( $args = array() ) {
			return $this->build_request( 'company/contacts/count', $args )->fetch();
		}

		/**
		 * get_company_contacts_by_id function.
		 *
		 * @access public
		 * @param int $contact_id
		 * @return void
		 */
		public function get_company_contacts_by_id( int $contact_id ) {
			return $this->build_request( "company/contacts/$contact_id" )->fetch();
		}

		/**
		 * delete_company_contacts function.
		 *
		 * @access public
		 * @param mixed $contact_id
		 * @return void
		 */
		public function delete_company_contacts( $contact_id ) {
			return $this->build_request( "company/contacts/$contact_id", $args, 'DELETE' )->fetch();
		}

		/**
		 * replace_company_contacts function.
		 *
		 * @access public
		 * @param mixed $contact_id
		 * @param array $args (default: array())
		 * @return void
		 */
		public function replace_company_contacts( $contact_id, $args = array() ) {
			return $this->build_request( "company/contacts/$contact_id", $args, 'PUT' )->fetch();
		}

		/**
		 * update_company_contacts function.
		 *
		 * @access public
		 * @param mixed $contact_id
		 * @param array $args (default: array())
		 * @return void
		 */
		public function update_company_contacts( $contact_id, $args = array() ) {
			return $this->build_request( "company/contacts/$contact_id", $args, 'PATCH' )->fetch();
		}

		/**
		 * get_company_contact_portal_security function.
		 *
		 * @access public
		 * @param mixed $contact_id
		 * @return void
		 */
		public function get_company_contact_portal_security( $contact_id ) {
			return $this->build_request( "company/contacts/$contact_id/portalSecurity" )->fetch();
		}

		/**
		 * request_company_contact_password function.
		 *
		 * @access public
		 * @return void
		 */
		public function request_company_contact_password() {
			return $this->build_request( 'company/contacts/requestPassword', null, 'POST' )->fetch();
		}

		/**
		 * validate_company_contacts_portal_credentials function.
		 *
		 * @access public
		 * @param mixed $email
		 * @param mixed $password
		 * @return void
		 */
		public function validate_company_contacts_portal_credentials( $email, $password ) {
			return $this->build_request( 'company/contacts/requestPassword', $args, 'POST' )->fetch();
		}

		/**
		 * get_company_contact_image function.
		 *
		 * @access public
		 * @param int $contact_id
		 * @param string $use_default_flag (default: '')
		 * @param string $last_modified (default: '')
		 * @return void
		 */
		public function get_company_contact_image( int $contact_id, $use_default_flag = '', $last_modified = '' ) {
			return $this->build_request( "company/contacts/$contact_id/image" )->fetch();
		}




		/* COMPANY - CUSTOM NOTES. */

		/**
		 * get_campaigns function.
		 *
		 * @access public
		 * @param array $args (default: array())
		 * @return void
		 */
		public function get_campaigns( $args = array() ) {
			return $this->build_request( 'marketing/campaigns', $args )->fetch();
		}

		/* EXPENSE. */

		/* FINANCE. */

		/* MARKETING. */

		/* PROCUREMENT. */

		/* SALES. */

		/**
		 * get_orders function.
		 *
		 * @access public
		 * @param array $args (default: array())
		 * @return void
		 */
		public function get_orders( $args = array() ) {
			return $this->build_request( 'sales/orders', $args )->fetch();
		}

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
		 * @param mixed  $ticket_id
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
		 * @param mixed  $ticket_id
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
		 * @param mixed  $ticket_id
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
		 * @param mixed  $id
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
		 * @param mixed  $ticket_id
		 * @param string $page (default: '')
		 * @param string $page_size (default: '')
		 * @return void
		 */
		public function get_tickets_products( $ticket_id, $args = array() ) {
			return $this->build_request( "service/tickets/$ticket_id/products", $args )->fetch();
		}

		/* EXPENSE. */

		/* FINANCE. */

		/* MARKETING. */

		/* PROCUREMENT. */

		/*
		 PROJECT. */
		/* @docs - https://developer.connectwise.com/manage/rest?a=Project */


		/* PROJECT CONTACTS. */

		/**
		 * get_project_contacts function.
		 *
		 * @access public
		 * @param mixed $project_id
		 * @param array $args (default: array())
		 * @return void
		 */
		public function get_project_contacts( $project_id, $args = array() ) {
			return $this->build_request( "project/projects/$project_id/contacts", $args )->fetch();
		}

		/**
		 * create_project_contacts function.
		 *
		 * @access public
		 * @param mixed $project_id
		 * @param array $args (default: array())
		 * @return void
		 */
		public function create_project_contacts( $project_id, $args = array() ) {
			return $this->build_request( "project/projects/$project_id/contacts", $args, 'POST' )->fetch();
		}

		/**
		 * get_project_contact_by_id function.
		 *
		 * @access public
		 * @param mixed $project_id
		 * @param mixed $contact_id
		 * @return void
		 */
		public function get_project_contact_by_id( $project_id, $contact_id ) {
			return $this->build_request( "project/projects/$project_id/contacts/$contact_id", $args )->fetch();
		}

		/**
		 * delete_project_contact function.
		 *
		 * @access public
		 * @param mixed $project_id
		 * @param mixed $contact_id
		 * @return void
		 */
		public function delete_project_contact( $project_id, $contact_id ) {
			return $this->build_request( "project/projects/$project_id/contacts/$contact_id", $args, 'DELETE' )->fetch();
		}

		/* PROJECT NOTES. */

		/**
		 * get_project_notes function.
		 *
		 * @access public
		 * @param mixed $project_id
		 * @param array $args (default: array())
		 * @return void
		 */
		public function get_project_notes( $project_id, $args = array() ) {
			return $this->build_request( "project/projects/$project_id/notes", $args )->fetch();
		}

		/**
		 * create_project_notes function.
		 *
		 * @access public
		 * @param mixed $project_id
		 * @param array $args (default: array())
		 * @return void
		 */
		public function create_project_notes( $project_id, $args = array() ) {
			return $this->build_request( "project/projects/$project_id/notes", $args, 'POST' )->fetch();
		}

		/**
		 * get_project_notes_count function.
		 *
		 * @access public
		 * @param mixed $project_id
		 * @param array $args (default: array())
		 * @return void
		 */
		public function get_project_notes_count( $project_id, $args = array() ) {
			return $this->build_request( "project/projects/$project_id/notes/count", $args )->fetch();
		}

		public function get_project_notes_by_id() {

		}

		public function delete_project_notes() {

		}

		public function replace_project_notes() {

		}

		public function update_project_notes() {

		}

		/* PROJECT PHASES. */

		public function get_project_phases() {

		}

		public function create_project_phases() {

		}

		public function count_project_phases() {

		}

		public function get_project_phases_by_id() {

		}

		public function delete_project_phases() {

		}

		public function replace_project_phases() {

		}

		public function update_project_phases() {

		}

		/* PROJECT STATUSES. */

		public function get_project_statuses() {

		}

		public function create_project_statuses() {

		}

		public function count_project_statuses() {

		}

		public function get_project_statuses_by_id() {

		}

		public function delete_project_statuses() {

		}

		public function replace_project_statuses() {

		}

		public function update_project_statuses() {

		}

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


		public function create_project() {

		}

		public function count_projects() {

		}

		public function get_project_by_id() {

		}

		public function delete_project() {

		}

		public function replace_project() {

		}

		public function update_project() {

		}

		/* PROJECTS TEAM MEMBERS. */

		public function get_project_team_members() {

		}

		public function create_project_team_members() {

		}

		public function count_project_team_members() {

		}

		public function get_project_team_members_by_id() {

		}

		public function delete_project_team_members() {

		}

		public function replace_project_team_members() {

		}

		public function update_project_team_members() {

		}


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
			return $this->build_request( 'time/entries', $args )->fetch();
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
			return $this->build_request( 'time/entries/count', $args )->fetch();
		}

	}
}
