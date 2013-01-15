<?php
/*
Plugin Name: Share
Plugin URI: http://www.williamyhuang.com
Description: Easily have your posts published to Scoop.it webservices. Also allows publishing to all the social media sites that Scoop.it supports.
Version: 1.0
Author: William Huang
Author URI: http://www.williamyhuang.com
License: GPL2
*/

/*
TODO:
Implement a modal pop up for links
*/


$share_database;

require_once('shareoptionsfunctions.php');
require_once('sharemetaboxfunctions.php');
require_once('sharescoopitfunctions.php');
require_once('sharedatabasefunctions.php');
require_once('urlshortener.php');


register_activation_hook( __FILE__, 'share_activation' );
register_deactivation_hook( __FILE__, 'share_deactivation' );
add_action( 'admin_init', 'share_init', 1 );
add_action( 'admin_menu', 'share_add_options_page' );
add_action( 'load-post-new.php', 'share_meta_box_setup' );
add_filter( 'plugin_action_links', 'share_action_links', 10, 2 );
add_action( 'admin_enqueue_scripts', 'share_enqueue_colorbox' );
add_action( 'admin_enqueue_scripts', 'share_enqueue_metabox' );
add_action( 'wp_logout', 'share_end_session' );
add_action( 'wp_login', 'share_end_session' );
add_action( 'wp_ajax_share_store_access_token', 'share_store_access_token_callback' );
add_action( 'wp_ajax_share_access_token_failed', 'share_access_token_failed_callback' );
add_action( 'wp_ajax_share_publish_post', 'share_publish_post_callback' );
add_action( 'draft_to_publish', 'share_publish' );

function share_init() {
	global $share_database;
	$user = wp_get_current_user();
	$share_database = new ShareDatabase($user->ID);
	if(!session_id())
        session_start();
}

function share_action_links( $links, $file) {
	if ( $file == plugin_basename( dirname(__FILE__).'/share.php' ) ) {
		$links[] = '<a href="admin.php?page=share-general-settings">'.__('Settings').'</a>';
	}
	return $links;
}

function share_activation () {
	add_option('share_options');
}

function share_deactivation () {
	global $share_database;
	share_end_session();
	$share_database->LogOut();
}

function share_meta_box_setup() {
	add_action( 'add_meta_boxes', 'share_add_meta_box' );
}

function share_enqueue_metabox($hook) {
	if( 'post-new.php' != $hook )
		return;
	wp_register_style( 'share_css', plugins_url('/css/share.css', __FILE__) );
	wp_enqueue_style( 'share_css' );
	wp_enqueue_script( 'share_my_script', plugins_url('/js/share.js', __FILE__), array( 'jquery' ) );
}

function share_enqueue_colorbox($hook) {
	if( 'settings_page_share-general-settings' != $hook )
		return;
    wp_enqueue_script( 'jquery_colorbox_script', plugins_url('/js/colorbox/jquery.colorbox.js', __FILE__), array( 'jquery' ));
	wp_enqueue_script( 'my_colorbox_script', plugins_url('/js/mycolorbox.js', __FILE__), array( 'jquery_colorbox_script' ) );
	wp_register_style( 'jquery_colorbox_css', plugins_url('/css/colorbox.css', __FILE__) );
	wp_enqueue_style( 'jquery_colorbox_css' );
}

function share_end_session() {
	if (session_id())
    	session_destroy();
}

?>