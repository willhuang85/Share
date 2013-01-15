<?php 
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

class ShareDatabase {

	private $table_name;
	private $user_id;
	private $platform;
	private $db;
	
	function __construct($userid) {
		global $wpdb;
		$this->db = $wpdb;
		$this->table_name = $this->db->prefix . "share_plugin";
		$this->user_id = $userid;	
		$sql = "CREATE TABLE IF NOT EXISTS $this->table_name (
				user_id BIGINT,
				PRIMARY KEY(user_id),
				logged_in int,
				access_token_value longtext,
				access_token_secret longtext,
				topic longtext,
				sharers longtext
				)
				COLLATE utf8_general_ci";
		
		dbDelta($sql);
		$this->insertRow();
	}
	
	function deleteTable() {
		$sql = "DROP TABLE IF EXISTS $this->table_name";
		$this->db->query($sql);
		dbDelta($sql);
	}
	
	private function insertRow() {
		$sql = "INSERT INTO $this->table_name (user_id, logged_in, access_token_value, access_token_secret, topic, sharers) VALUES ('$this->user_id', 0, '', '', '', '') ON DUPLICATE KEY UPDATE user_id = $this->user_id";
		dbDelta($sql);
	}
	
	function setAccessTokenValue($value) {
		$sql = "UPDATE $this->table_name SET access_token_value = '$value' WHERE user_id = $this->user_id";
		$this->db->query($sql);	
	}
		
	function setAccessTokenSecret($secret) {
		$sql = "UPDATE $this->table_name SET access_token_secret = '$secret' WHERE user_id = $this->user_id";
		$this->db->query($sql);
	}
	
	function setTopic($topic) {
		$sql = "UPDATE $this->table_name SET topic = '$topic' WHERE user_id = $this->user_id";
		$this->db->query($sql);
	}
	
	function setSharers($sharers) {
		$sql = "UPDATE $this->table_name SET sharers = '$sharers' WHERE user_id = $this->user_id";
		$this->db->query($sql);
	}
	
	function LogIn() {
		$sql = "UPDATE $this->table_name SET logged_in = 1 WHERE user_id = $this->user_id";
		$this->db->query($sql);
	}
	
	function LogOut() {
		$sql = "UPDATE $this->table_name SET logged_in = 0 WHERE user_id = $this->user_id";
		$this->db->query($sql);
		$this->setAccessTokenValue('');
		$this->setAccessTokenSecret('');
		$this->setTopic('');
		$this->setSharers('');
	}
	
	function getAccessTokenValue() {
		$sql = "SELECT access_token_value FROM $this->table_name WHERE user_id = $this->user_id";
		$row = $this->db->get_row($sql, ARRAY_A);
		return $row['access_token_value'];
	}
	
	function getAccessTokenSecret() {
		$sql = "SELECT access_token_secret FROM $this->table_name WHERE user_id = $this->user_id";
		$row = $this->db->get_row($sql, ARRAY_A);
		return $row['access_token_secret'];
	}
	
	function getTopic() {
		$sql = "SELECT topic FROM $this->table_name WHERE user_id = $this->user_id";
		$row = $this->db->get_row($sql, ARRAY_A);
		return $row['topic'];
	}
	
	function getSharers() {
		$sql = "SELECT sharers FROM $this->table_name WHERE user_id = $this->user_id";
		$row = $this->db->get_row($sql, ARRAY_A);
		return $row['sharers'];
	}
	
	function isLoggedIn() {
		$sql = "SELECT logged_in FROM $this->table_name WHERE user_id = $this->user_id";
		$row = $this->db->get_row($sql, ARRAY_A);
		return $row['logged_in'];
	}
}
?>