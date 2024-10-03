<?php

require_once(dirname(__File__).'/get_order_data.php');


function send_order_data_to_api_async($order_id) {
	// Schedule the API call to be handled after the checkout completes

	//------------------------------------------------------------------------
	//--------------------------------log-------------------------------------
	//------------------------------------------------------------------------
	if (__IFA_DEBUG__=='TRUE') {
		error_log("<pre>send_order_data_to_api_async wp_next_scheduled orderId : $order_id</pre>");
	}
	//------------------------------------------------------------------------
	//------------------------------------------------------------------------



	if (!wp_next_scheduled('send_order_data_to_api', array($order_id))) {
		//------------------------------------------------------------------------
		//--------------------------------log-------------------------------------
		//------------------------------------------------------------------------
		if (__IFA_DEBUG__=='TRUE') {
			error_log("<pre>send_order_data_to_api_async yes wp_next_scheduled orderId : $order_id</pre>");
		}
		//------------------------------------------------------------------------
		//------------------------------------------------------------------------


		wp_schedule_single_event(time() + 2, 'send_order_data_to_api', array($order_id));
	}
}
add_action('send_order_data_to_api', 'send_order_data_to_api', 10, 1); // Hook for scheduling the API call

function send_order_data_to_api($order_id) {
    global $wpdb;
    $api_url = __TARGET_URL__;  // Replace with actual API URL


	//------------------------------------------------------------------------
	//--------------------------------log-------------------------------------
	//------------------------------------------------------------------------
	if (__IFA_DEBUG__=='TRUE') {
		error_log("<pre>send_order_data_to_api called $api_url orderId : $order_id</pre>");
	}
	//------------------------------------------------------------------------
	//------------------------------------------------------------------------


	$order_data = get_full_order_data($order_id); // Retrieve full order data

	//------------------------------------------------------------------------
	//--------------------------------log-------------------------------------
	//------------------------------------------------------------------------
	if (__IFA_DEBUG__=='TRUE') {
		error_log("<pre>send_order_data_to_api get_full_order_data $order_data</pre>");
	}
	//------------------------------------------------------------------------
	//------------------------------------------------------------------------



	$table = $wpdb->prefix . 'order_queue';

	//------------------------------------------------------------------------
	//--------------------------------log-------------------------------------
	//------------------------------------------------------------------------
	if (__IFA_DEBUG__=='TRUE') {
		error_log("<pre>send_order_data_to_api calling api wp_json_encode(order_data)</pre>");
		error_log("<pre>send_order_data_to_api calling api wp_json_encode(order_data):$order_data</pre>");
	}
	//------------------------------------------------------------------------
	//------------------------------------------------------------------------


	try {


		//------------------------------------------------------------------------
		//--------------------------------log-------------------------------------
		//------------------------------------------------------------------------
		if (__IFA_DEBUG__=='TRUE') {
			error_log("<pre>send_order_data_to_api trying to send data: ".wp_json_encode($order_data)."</pre>");
		}
		//------------------------------------------------------------------------
		//------------------------------------------------------------------------


		$response = wp_remote_post($api_url, array(
			'method'    => 'POST',
			'body'      => wp_json_encode($order_data),
			'headers'   => array(
				'Content-Type' => 'application/json',
			),
			'timeout'   => 0.01,  // Make it a non-blocking request

		));


		//------------------------------------------------------------------------
		//--------------------------------log-------------------------------------
		//------------------------------------------------------------------------
		if (__IFA_DEBUG__=='TRUE') {
			error_log("<pre>send_order_data_to_api api response: ".print_r($response)."</pre>");
			error_log("<pre>send_order_data_to_api api response: ".is_wp_error($response)."</pre>");
			error_log("<pre>send_order_data_to_api api response: ".wp_remote_retrieve_response_code($response)."</pre>");
		}
		//------------------------------------------------------------------------
		//------------------------------------------------------------------------

	}catch (exception $e){

		//------------------------------------------------------------------------
		//--------------------------------log-------------------------------------
		//------------------------------------------------------------------------
		if (__IFA_DEBUG__=='TRUE') {
			error_log("<pre>send_order_data_to_api api catch (exception e): ".print_r($e)."</pre>");
		}
		//------------------------------------------------------------------------
		//------------------------------------------------------------------------

	}





	//------------------------------------------------------------------------
	//--------------------------------log-------------------------------------
	//------------------------------------------------------------------------
	if (__IFA_DEBUG__=='TRUE') {
		error_log("<pre>send_order_data_to_api api response: ".print_r($response)."</pre>");
		error_log("<pre>send_order_data_to_api api response: ".is_wp_error($response)."</pre>");
		error_log("<pre>send_order_data_to_api api response: ".wp_remote_retrieve_response_code($response)."</pre>");
	}
	//------------------------------------------------------------------------
	//------------------------------------------------------------------------


	// Handle the API response
	if (!$response || is_wp_error($response) || wp_remote_retrieve_response_code($response) != 200) {
		// If the API call fails, store the order in the queue


		//------------------------------------------------------------------------
		//--------------------------------log-------------------------------------
		//------------------------------------------------------------------------
		if (__IFA_DEBUG__=='TRUE') {
			error_log("<pre>send_order_data_to_api is_wp_error(response) || wp_remote_retrieve_response_code(response) != 200)</pre>");
			error_log("<pre>wpdb->insert $order_id</pre>");
			error_log("<pre>wpdb->insert $order_data</pre>");
			error_log("<pre>wpdb->insert ".print_r($order_data)."</pre>");
			error_log("<pre>wpdb->insert ".wp_json_encode($order_data)."</pre>");
		}
		//------------------------------------------------------------------------
		//------------------------------------------------------------------------


		$wpdb->insert(
			$table,
			array(
				'order_id'   => $order_id,
				'order_data' => wp_json_encode($order_data), // Save as JSON
				'status'     => 'pending',
			),
			array(
				'%d',
				'%s',
				'%s',
			)
		);



		error_log('API unavailable, order data queued for later processing.');
	} else {
		error_log('Order data sent successfully!');


		//------------------------------------------------------------------------
		//--------------------------------log-------------------------------------
		//------------------------------------------------------------------------
		if (__IFA_DEBUG__=='TRUE') {
			error_log("<pre>send_order_data_to_api wpdb->delete</pre>");
			error_log("<pre>wpdb->insert $order_id</pre>");
			error_log("<pre>wpdb->insert $table</pre>");
		}
		//------------------------------------------------------------------------
		//------------------------------------------------------------------------

		$wpdb->delete(
			$table,
			array(
				'order_id'   => $order_id
			)
		);


	}



	//------------------------------------------------------------------------
	//--------------------------------log-------------------------------------
	//------------------------------------------------------------------------
	if (__IFA_DEBUG__=='TRUE') {
		error_log("<pre>send_order_data_to_api done </pre>");
	}
	//------------------------------------------------------------------------
	//------------------------------------------------------------------------

}

?>
