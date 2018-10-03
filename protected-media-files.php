<?php
/*
Plugin Name: Protected Media Posts
Description: This is a Plugin for Naturopathic Services. How to use it, very simple just active this plugin, It will create a admin menu tab named "Protected Posts". Post your media files under "Protected Posts" tab
Version: 0.0.10
Author: Md. Mostak Shahid
*/


require_once ( plugin_dir_path( __FILE__ ) . 'protected-media-files-functions.php' );
require_once ( plugin_dir_path( __FILE__ ) . 'protected-media-files-settings.php' );
require_once ( plugin_dir_path( __FILE__ ) . 'protected-media-files-post-types.php' );
require_once ( plugin_dir_path( __FILE__ ) . 'protected-media-files-taxonomy.php' );

require_once( plugin_dir_path( __FILE__ ) . 'plugins/aq_resizer.php');
require_once( plugin_dir_path( __FILE__ ) . 'plugins/metabox/init.php');
require_once( plugin_dir_path( __FILE__ ) . 'metaboxes.php');

require_once('plugins/update/plugin-update-checker.php');
$pluginInit = Puc_v4_Factory::buildUpdateChecker(
	'https://raw.githubusercontent.com/mostak-shahid/update/master/protected-media-files.json',
	__FILE__,
	'protected-media-files'
);



