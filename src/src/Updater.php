<?php
namespace Niteoweb\RebuildPages;


class Updater {
	public $plugin_path;
	public $plugin_slug;
	public $slug;
	public $update_url;

	function __construct( $plugin_path, $url ) {
		$this->update_url  = $url;
		$this->plugin_path = $plugin_path;
		$this->plugin_slug = plugin_basename( $plugin_path );
		$this->slug        = str_replace( '.php', '', $this->plugin_slug );
		// define the alternative API for updating checking
		add_filter( 'pre_set_site_transient_update_plugins', array( &$this, 'checkUpdate' ) );
	}

	public function checkUpdate( $transient ) {
		if ( empty( $transient->checked ) ) {
			return $transient;
		}
		// Get the remote version
		$versions       = $this->getRemoteInformation();
		$remote_version = $versions[0];
		if ( $remote_version ) {
			$plugin_info = get_plugin_data( $this->plugin_path, false );
			// Set the class public variables
			$version = str_replace( 'v', '', $remote_version->tag_name );
			// If a newer version is available, add the update
			if ( version_compare( $plugin_info["Version"], $version, '<' ) ) {
				$obj                                       = new \stdClass();
				$obj->slug                                 = $this->slug;
				$obj->new_version                          = $version;
				$obj->url                                  = $this->update_url;
				$obj->package                              = $remote_version->assets[0]->browser_download_url;
				$transient->response[ $this->plugin_slug ] = $obj;
			}
		}

		return $transient;
	}

	public function getRemoteInformation() {
		$request = wp_remote_get( $this->update_url );
		if ( ! is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) === 200 ) {
			return json_decode( $request['body'] );
		}

		return [ ];
	}
}