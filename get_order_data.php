<?php
function get_full_order_data($order_id) {
	$order = wc_get_order($order_id);

	if (!$order) {
		return false;
	}

	// Get customer data
	$customer_id = $order->get_customer_id();
	$customer = new WC_Customer($customer_id);

	// Get additional data
	$order_data = array(
		'order_id'               => $order->get_id(),
		'order_status'               => $order->get_status(),
		'customer_mobile'      => $customer->get_username(), // Include customer's username
		'customer_email'      => $customer->get_email(), // Include customer's username
		'customer_id'      => $customer->get_id(), // Include customer's username

	);


	// Get product line items
	foreach ($order->get_items() as $item_id => $item) {
		$product = $item->get_product();

		$order_data['line_items'][] = array(
			'product_id'            => $product->get_id(),
			'name'                  => $item->get_name()
		);
	}

	// Add other useful order data if needed
	$order_data['date_modified'] = $order->get_date_modified()->date_i18n('Y-m-d H:i:s'); // Date modified

	return $order_data;
}




?>
