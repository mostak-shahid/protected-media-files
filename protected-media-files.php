<?php
/*
Plugin Name: Protected Media Posts
Description: This is a Plugin for Naturopathic Services. How to use it, very simple just active this plugin, It will create a admin menu tab named "Protected Posts". Post your media files under "Protected Posts" tab
Version: 0.0.13
Author: Md. Mostak Shahid
*/


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Define PMF_PLUGIN_FILE.
if ( ! defined( 'PMF_PLUGIN_FILE' ) ) {
	define( 'PMF_PLUGIN_FILE', __FILE__ );
}
// Define PMF_SETTINGS.
if ( ! defined( 'PMF_SETTINGS' ) ) {
	define( 'PMF_SETTINGS', admin_url('/edit.php?post_type=p_file&page=pmf_settings') );
}

$plugin = plugin_basename(PMF_PLUGIN_FILE); 
require_once ( plugin_dir_path( PMF_PLUGIN_FILE ) . 'protected-media-files-functions.php' );
require_once ( plugin_dir_path( PMF_PLUGIN_FILE ) . 'protected-media-files-settings.php' );
require_once ( plugin_dir_path( PMF_PLUGIN_FILE ) . 'protected-media-files-post-types.php' );
require_once ( plugin_dir_path( PMF_PLUGIN_FILE ) . 'protected-media-files-taxonomy.php' );

require_once( plugin_dir_path( PMF_PLUGIN_FILE ) . 'plugins/aq_resizer.php');
require_once( plugin_dir_path( PMF_PLUGIN_FILE ) . 'plugins/metabox/init.php');
require_once( plugin_dir_path( PMF_PLUGIN_FILE ) . 'metaboxes.php');

require_once('plugins/update/plugin-update-checker.php');
$pluginInit = Puc_v4_Factory::buildUpdateChecker(
	'https://raw.githubusercontent.com/mostak-shahid/update/master/protected-media-files.json',
	PMF_PLUGIN_FILE,
	'protected-media-files'
);


register_activation_hook(PMF_PLUGIN_FILE, 'my_plugin_activate');
add_action('admin_init', 'my_plugin_redirect');
 
function my_plugin_activate() {
    add_option('my_plugin_do_activation_redirect', true);
}
 
function my_plugin_redirect() {
    if (get_option('my_plugin_do_activation_redirect', false)) {
        delete_option('my_plugin_do_activation_redirect');
        if(!isset($_GET['activate-multi'])){
            wp_safe_redirect(PMF_SETTINGS);
        }
    }
}

// Add settings link on plugin page
function your_plugin_settings_link($links) { 
  $settings_link = '<a href="'.PMF_SETTINGS.'">Settings</a>'; 
  array_unshift($links, $settings_link); 
  return $links; 
} 
add_filter("plugin_action_links_$plugin", 'your_plugin_settings_link' );



