<?php

require_once(dirname(__File__).'/send_to_api.php');

function retry_failed_order_requests() {
    global $wpdb;

	$table_name = $wpdb->prefix . 'order_queue';

	$pending_requests= $wpdb->get_results("select * from $table_name where status = 'pending'");

	foreach($pending_requests as $req) {
		send_order_data_to_api($req->order_id);
	}

}

?>
