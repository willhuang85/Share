<?php

function share_add_options_page() {
	add_options_page('Share Settings', 'Share', 'manage_options', 'share-general-settings', 'share_options_form');
	register_setting( 'share_plugin_options', 'share_options', 'share_validate_options' );
	add_settings_section('share_key_secret', 'Scoop.it API', 'scoopit_text', 'share-general-settings');
	add_settings_field('share_field_key', 'Scoop.it Consumer Key', 'scoopit_field_key', 'share-general-settings', 'share_key_secret');
	add_settings_field('share_field_secret', 'Scoop.it Consumer Secret', 'scoopit_field_secret', 'share-general-settings', 'share_key_secret');
	add_settings_field('share_field_login', '', 'scoopit_field_login', 'share-general-settings', 'share_key_secret');
}

function scoopit_text() {
	echo '<p>Enter in your consumer key and secret in the appropriate fields.<br/>';
	echo 'You can obtain a consumer key and secret from <a href="https://www.scoop.it/apps" target="_blank">Scoop.it</a></p>'; 
}

function scoopit_field_key() {
	$options = get_option('share_options'); 
	$key = is_array($options) ? $options['key'] : "";
	echo '<input type="text" name="share_options[key]" value="'.$key.'" class="regular-text"/>';
}

function scoopit_field_login() {
	global $share_database;
	$loggedin = $share_database->isLoggedIn();
	$options = get_option('share_options');
	$key = '';
	$secret = '';
	if (is_array($options)) {
		$key = array_key_exists('key',$options) ? $options['key'] : '';
		$secret = array_key_exists('secret',$options) ? $options['secret'] : '';
	}
		
	$show = !(empty($key) || empty($secret));

	if ($show) {
		$show = $show && (!$loggedin ? true : false);
	}
		
	$link_url = plugins_url('/oauth/login_with_scoopit.php?share_consumerkey='.$key.'&share_consumersecret='.$secret, __FILE__);
	echo '<div class="share_login_text" style="display:'.($show ? 'inline' : 'none').'">';
	echo '<a href="'.$link_url.'" class="scoopit_iframe">'.__('Login to Scoop.it', 'share_plugin_domain').'</a>';
}

function scoopit_field_secret() {
	$options = get_option('share_options'); 
	$secret = is_array($options) ? $options['secret'] : "";
	echo '<input type="text" name="share_options[secret]" value="'.$secret.'" class="regular-text"/>';
}

function share_validate_options($input) {
	$input['key'] =  wp_filter_nohtml_kses($input['key']);
	$input['secret'] =  wp_filter_nohtml_kses($input['secret']);
	return $input;
}

function share_options_form() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	echo '<div class="wrap">';
	screen_icon('options-general');
	echo '<h2>Share General Settings</h2>';
	echo '<a href="http://www.scoop.it" alt="Powered By Scoop.it">';
	echo '<img src="'.plugins_url( 'images/scoopit.png', __FILE__ ).'" alt="Powered By Scoop.It">';
	echo '</a>';
	echo '<form method="post" action="options.php">';
	settings_fields( 'share_plugin_options' );
	do_settings_sections('share-general-settings');
	echo '<p class="submit">';
	echo '<input type="submit" class="button-primary" value="'.__('Save Changes').'" />';
	echo '</p>';
	echo '</form>';
	echo '</div>';
}

function share_store_access_token_callback() {	
	global $share_database;
	$accesstoken = $_SESSION['OAUTH_ACCESS_TOKEN']['https://www.scoop.it/oauth/access'];
	session_destroy();
	$share_database->setAccessTokenValue($accesstoken['value']);
	$share_database->setAccessTokenSecret($accesstoken['secret']);
	$share_database->LogIn();
	die();
}

function share_access_token_failed_callback() {	
	global $share_database;
	session_destroy();
	die();
}
?>