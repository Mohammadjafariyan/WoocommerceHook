<?php

require_once(dirname(__File__).'/send_to_api.php');

function get_order_by_id_api(WP_rest_Request $request)
{
	$order_id = $request->get_param('id');

	//------------------------------------------------------------------------
	//--------------------------------log-------------------------------------
	//------------------------------------------------------------------------
	if (__IFA_DEBUG__=='TRUE') {
		error_log("<pre>get_order_by_id_api".print_r($request)."</pre>");
		error_log("<pre>get_order_by_id_api".$order_id."</pre>");
	}
	//------------------------------------------------------------------------
	//------------------------------------------------------------------------

	send_order_data_to_api($order_id);

}
function ifa_register_getbyId_rest_route(){

	//------------------------------------------------------------------------
	//--------------------------------log-------------------------------------
	//------------------------------------------------------------------------
	if (__IFA_DEBUG__=='TRUE') {
		error_log("<pre>ifa_register_getbyId_rest_route </pre>");
	}
	//------------------------------------------------------------------------
	//------------------------------------------------------------------------


	register_rest_route('ifasanat/v1', '/get_order_by_id', array(
		'methods' => 'GET',
		'callback' => 'get_order_by_id_api',
		'permission_callback' => '__return_true'
	));
}




?>
