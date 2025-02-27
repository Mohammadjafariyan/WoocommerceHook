<?php

require_once(dirname(__File__).'/send_to_api.php');

function get_all_orders_restful_api() {

	$query = new WP_Order_Query(array(
		'limit'=>-1
	));

	$orders= $query->get_orders();


	//------------------------------------------------------------------------
	//--------------------------------log-------------------------------------
	//------------------------------------------------------------------------
	if (__IFA_DEBUG__=='TRUE') {
		error_log("<pre>get_all_orders_restful_api ".print_r($orders)."</pre>");
	}
	//------------------------------------------------------------------------
	//------------------------------------------------------------------------


	foreach($orders as $order) {

		//------------------------------------------------------------------------
		//--------------------------------log-------------------------------------
		//------------------------------------------------------------------------
		if (__IFA_DEBUG__=='TRUE') {
			error_log("<pre>get_all_orders_restful_api calling send_order_data_to_api".print_r($order->order_id)."</pre>");
		}
		//------------------------------------------------------------------------
		//------------------------------------------------------------------------


		send_order_data_to_api($order->order_id);
	}

}

function ifa_register_rest_route(){


	//------------------------------------------------------------------------
	//--------------------------------log-------------------------------------
	//------------------------------------------------------------------------
	if (__IFA_DEBUG__=='TRUE') {
		error_log("<pre>ifa_register_rest_route </pre>");
	}
	//------------------------------------------------------------------------
	//------------------------------------------------------------------------


	register_rest_route('ifasanat/v1', '/get_all_orders', array(
		'methods' => 'GET',
		'callback' => 'get_all_orders_restful_api',
		'permission_callback' => '__return_true'
	));

}




?>
