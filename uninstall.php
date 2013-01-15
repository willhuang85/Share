<?php
delete_option('share_options');

global $wpdb;
$table_name = $wpdb->prefix . "share_plugin";
$sql = "DROP TABLE IF EXISTS $table_name";
$wpdb->query($sql);
?>