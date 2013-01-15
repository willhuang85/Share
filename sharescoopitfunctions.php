<?php
require_once('oauth/oauth_client.php');
require_once('oauth/http.php');

class Sharescoopit {
	
	private $client;
	function __construct($key,$secret,$token_value,$token_secret) {
		$this->client = new oauth_client_class;
		$this->client->debug = 1;
		$this->client->debug_http = 0;
		$this->client->server = 'Scoop.it';
		$this->client->client_id = $key;
		$this->client->client_secret = $secret;
		$this->client->Initialize();
		$this->client->access_token = $token_value;
		$this->client->access_token_secret = $token_secret;
	}
	
	private function getSharers() {
		$this->client->CallAPI('http://www.scoop.it/api/1/profile', 'GET', array(), array('FailOnAccessError'=>true), $response);
		return $response->user->sharers;
	}
	
	//Need 'name' and 'id' from this array
	private function getTopics() {
		$this->client->CallAPI('http://www.scoop.it/api/1/profile', 'GET', array(), array('FailOnAccessError'=>true), $response);
		return $response->user->curatedTopics;
	}
	
	//Returns an array that contains share arrays
	private function createSharers($sharers, $shareText) {
		$arr = array();
		foreach ($sharers as &$v)
			array_push($arr, $this->createShare($v, $shareText));
		return $arr;
	}
	
	//Creates an array for each Share ie. facebook, twitter
	private function createShare($sharer, $shareText) {
		return array('sharerId' => $sharer[0], 'cnxId' => str_replace("/","",$sharer[1]), 'text' => $shareText);
	}
	
	public function testToken() {
		return $this->client->CallAPI(
			'http://www.scoop.it/api/1/profile', 
			'GET', array(), array('FailOnAccessError'=>true), $user);
	}
	
	public function metaboxMainContent() {
		echo '<div class="share_select_topic">';
		echo '<select>';
		echo '<option name="topics" value="">'.__('Select a topic', 'share_plugin_domain').'</option>';
		$topics = $this->getTopics();
		foreach($topics as &$v) {
			echo '<option name="topics" value="'.$v->id.'">'.$v->name.'</option>';
		}
		echo '</select>';
		echo '</div>';
		$sharers = $this->getSharers();
		echo '<div class="share_sharers_list">';
		echo '<p>'.__('Select Scoop.it sharing options:','share_plugin_domain').'</p>';

		echo '<ul>';
		foreach ($sharers as &$v) {
			echo '<li>';
			echo '<input type="checkbox" name="'.$v->sharerId.'" value='.$v->cnxId.'/> ';
			echo '<label for="'.$v->sharerId.'"><img src="'.plugins_url('images/'.$v->sharerName.'.png', __FILE__).'"/></label>';
			echo '</li>';
		}
		echo '</ul>';
		echo '</div>';
	}
	
	public function publish($title, $content, $shareOnText, $topic, $sharers) {
		// $json = json_encode($this->createSharers($sharers, $shareOnText));
		$json = json_encode(array());
		$args = array('action'=>"create", 'title'=>$title, 'url'=> '', 'content'=> $content, 'imageUrl'=>'','topicId' => $topic, 'shareOn'=>$json);
		return $this->client->CallAPI(
			'http://www.scoop.it/api/1/post', 
			'POST', $args, array('FailOnAccessError'=>true), $response);
	}
	
	
}
?>