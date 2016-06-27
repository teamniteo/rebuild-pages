<?php
namespace Niteoweb\RebuildPages;


class App {

	public $defaults = array(
		'Title' => null,
		'URL'   => null
	);

	public $log = array();


	public function __construct() {
		add_action( 'admin_menu', array( $this, 'adminMenu' ) );
		add_filter( 'request', array( $this, 'injectPages' ) );
		add_filter( 'get_page_uri', array( $this, 'permalinkFix' ), 2, 10 );

	}

	function adminMenu() {
		add_management_page( 'Rebuild Pages', 'Rebuild Pages', 'publish_posts', 'rebuild_pages', array(
			$this,
			'form'
		) );
	}

	/**
	 *
	 * Plugin's handler for submit form
	 *
	 * @return void
	 *
	 */
	function form() {

		$opt_min_backlinks = $this->processOption( 'ebn_min_backlinks', 0, $_POST );
		$nonce             = wp_create_nonce( 'majestic_importer_nonce' );

		if ( 'POST' == $_SERVER['REQUEST_METHOD'] ) {
			$this->handleUploadedData( compact( 'opt_min_backlinks' ) );
		}

		include 'view/settings.php';

	}

	/**
	 *
	 * Page injector based on custom url
	 *
	 * @param $query_vars object
	 *
	 * @return object
	 */
	function injectPages( $query_vars ) {
		global $wpdb, $wp_query;

		if ( empty( $wp_query->post ) ) {
			$url     = parse_url( get_bloginfo( 'url' ) );
			$url     = isset( $url['path'] ) ? $url['path'] : '';
			$request = ltrim( substr( $_SERVER['REQUEST_URI'], strlen( $url ) ), '/' );
			if ( ( $pos = strpos( $request, "?" ) ) ) {
				$request = substr( $request, 0, $pos );
			}
			$sql = $wpdb->prepare(
				"SELECT $wpdb->posts.ID, $wpdb->postmeta.meta_value as custom_permalink FROM $wpdb->posts  " .
				"LEFT JOIN $wpdb->postmeta ON ($wpdb->posts.ID = $wpdb->postmeta.post_id) WHERE " .
				"  meta_key = 'custom_permalink' AND " .
				"  meta_value = %s" .
				"  AND post_status = 'publish' AND post_type = 'page' LIMIT 1",
				$request );

			$posts = $wpdb->get_results( $sql );

			if ( count( $posts ) ) {
				$query_vars = array( "page" => "", "page_id" => $posts[0]->ID );
			}
		}

		return $query_vars;
	}

	/**
	 *
	 * URL injector based on custom url
	 *
	 * @return string
	 */
	function permalinkFix( $uri, $page ) {
		if(strrpos($page->guid , "http://", -strlen($page->guid )) !== false){
			return str_replace( "http://", "", $page->guid );
		}
		return $uri;
	}

	/**
	 *
	 * Determine value of option $name from database, $default value or $params,
	 * save it to the db if needed and return it.
	 *
	 * @param string $name
	 * @param mixed $default
	 * @param array $params
	 *
	 * @return string
	 *
	 */
	private function processOption( $name, $default, $params ) {
		if ( array_key_exists( $name, $params ) ) {
			$value = stripslashes( $params[ $name ] );
		} else {
			$value = $default;
		}

		return $value;
	}

	/**
	 *
	 * Handle POST submission
	 *
	 * @param array $options
	 *
	 * @return void
	 *
	 */
	private function handleUploadedData( $options ) {

		// Check if we have the correct nonce value for the form, else ignore the whole request
		if ( ! wp_verify_nonce( $_POST['ebn_nonce'], 'majestic_importer_nonce' ) ) {
			$this->log['error'][] = 'Authentication failed. Please refresh the page and try again.';
			$this->showLog();

			return;
		}

		if ( empty( $_FILES['ebn_import']['tmp_name'] ) ) {
			$this->log['error'][] = 'No file uploaded, aborting.';
			$this->showLog();

			return;
		}

		if ( ! current_user_can( 'publish_pages' ) ) {
			$this->log['error'][] = 'You don\'t have the permissions to publish pages. Please contact the blog\'s administrator.';
			$this->showLog();

			return;
		}


		$time_start     = microtime( true );
		$csvParser      = new CSVParser( $this->log, $options );
		$imported_posts = $csvParser->parseUploadedMajesticFile();

		$imported = 0;

		foreach ( $imported_posts as $post ) {
			$id = $this->create_post( $post, $options, get_home_url() );
			if ( $id ) {
				$imported ++;
			}
		}

		$exec_time = microtime( true ) - $time_start;

		$this->log['notice'][] = sprintf( "<b>Imported {$imported} pages in %.2f seconds.</b>", $exec_time );
		$this->showLog();

	}

	function showLog() {

		if ( ! empty( $this->log ) ) {

			$show_error  = ( ! empty( $this->log['error'] ) ) ? true : false;
			$show_notice = ( ! empty( $this->log['notice'] ) ) ? true : false;

			include 'view/log.php';

			$this->log = array();

		}

	}

	/**
	 *
	 * Create new WordPress page.
	 *
	 * @param  [object] $post     Post object, class rebuildPagesMajestic_Post
	 * @param  [array]  $options  Array of plugin options.
	 * @param  [string] $home_url Home url of WordPress blog
	 *
	 * @return [int | null] Returnes created post id or null if failed.
	 *
	 */
	private function create_post( $post, $options, $home_url ) {

		if ( $post ) {
			$guid = parse_url( $post->getUrl(), PHP_URL_PATH );
			$guid = ltrim( $guid, '/' );

			if ( strlen( $guid ) > 1 ) {

				$post_content = sprintf( 'Oops. Page not found. Go to <a href="%s">homepage</a>', $home_url );

				$new_post = array(
					'post_title'   => convert_chars( $guid ),
					'post_content' => $post_content,
					'post_status'  => 'publish',
					'post_type'    => 'page',
					'guid'         => $guid,
					'post_name'    => $guid
				);

				$id = wp_insert_post( $new_post );
				update_post_meta( $id, 'custom_permalink', $guid );

				return $id;
			}

		}

		return null;

	}

}

