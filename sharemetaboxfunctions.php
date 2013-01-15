<?php

function share_add_meta_box() {
	add_meta_box(
	'scoopit-post-class',			// Unique ID
	'Share',						// Title
	'share_meta_box_content',	// Callback function
	'post',							// Admin page (or post type)
	'side',							// Context
	'high'							// Priority
	);
}

function share_meta_box_content() { 
	wp_nonce_field( plugin_basename( __FILE__ ), 'share_noncename' );
	global $share_database;
	$shareoptions = get_option('share_options');
	if ($shareoptions != false) {
		$consumersecret = trim($shareoptions['secret']);
		$consumerkey = trim($shareoptions['key']);
	}
	
	$accesstokenvalue = $share_database->getAccessTokenValue();
	$accesstokensecret = $share_database->getAccessTokenSecret();
	if (!empty($accesstokenvalue) && !empty($accesstokensecret)) {
		$share_scoopit = new Sharescoopit($consumerkey,$consumersecret,$accesstokenvalue,$accesstokensecret);
		if ($share_scoopit->testToken() == 0)
			$share_database->LogOut();
	}
	
	if ($share_database->isLoggedIn() && !(empty($consumerkey) || empty($consumersecret))) {
		$share_scoopit->metaboxMainContent();
	} else {
		echo '<p>'.__('Please go to the Share ', 'share_plugin_domain').'<a href="admin.php?page=share-general-settings">'.__('settings page', 'share_plugin_domain').'</a></p>';
	}
}

function share_publish_post_callback() {
	global $share_database;
	$share_scoopit_publish = $_POST['share_sharers'];
	$scoopit_topic = array_shift($share_scoopit_publish);
	$share_database->setTopic($scoopit_topic);
	if (!empty($share_scoopit_publish))
		$share_database->setSharers(serialize($share_scoopit_publish));
	die();
}

function share_publish() {
	global $share_database;
	$title = get_the_title(get_the_id());
	$content = get_post_field('post_content', get_the_id());
	$url = get_permalink(get_the_ID());
	$shareOnText = custom_excerpt($content);
	
	$topic = $share_database->getTopic();
	$sharers = $share_database->getSharers();
	if (!empty($sharers))
		$sharers = unserialize($sharers);
	$consumerkey = get_option('share_options')['key'];
	$consumersecret = get_option('share_options')['secret'];
	$accesstokenvalue = $share_database->getAccessTokenValue();
	$accesstokensecret = $share_database->getAccessTokenSecret();
	
	if (!empty($topic)) {
		$share_scoopit = new Sharescoopit($consumerkey,$consumersecret,$accesstokenvalue,$accesstokensecret);
		// $share_scoopit->testToken();
		$share_scoopit->publish($title, $content, $shareOnText, $topic, $sharers);
		// 		$share_database->setTopic('');
		// 		$share_database->setSharers('');
	}
}

function custom_excerpt($content) {
	$text = substr($content, 0, 100);
	$ellip ='';
	if (strlen($content) > 100)
		$ellip='…';
	return $text . $ellip;
}
?>