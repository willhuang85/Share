<?php
require('http.php');
require('oauth_client.php');

if (!session_id())
	session_start();
if (array_key_exists('share_consumerkey',$_GET) && array_key_exists('share_consumersecret',$_GET)) {
	$_SESSION['share_consumerkey'] = $_GET['share_consumerkey'];
	$_SESSION['share_consumersecret'] = $_GET['share_consumersecret'];
}

$client = new oauth_client_class;
$client->debug = 0;
$client->debug_http = 0;
$client->server = 'Scoop.it';
$client->redirect_uri = 'http://'.$_SERVER['HTTP_HOST'].
	dirname(strtok($_SERVER['REQUEST_URI'],'?')).'/login_with_scoopit.php';


$client->client_id = $_SESSION['share_consumerkey'];
$client->client_secret = $_SESSION['share_consumersecret'];
		
if($success = $client->Initialize()) {
	$success = $client->Process();
	$success = $client->Finalize($success);
}
		
if (strlen($client->access_token)) {
	echo "<script>parent.jQuery.fn.colorbox.closeSuccess(); </script>";
} else {
	echo "<script>parent.jQuery.fn.colorbox.closeFail(); </script>";
}
?>