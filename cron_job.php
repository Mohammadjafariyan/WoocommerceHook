<?php

include_once(dirname(__FILE__).'/retry_failed_order_requests.php');

function ifa_retry_failed_requests()
{
	if(!wp_next_scheduled("retry_failed_order_requests")){
		wp_schedule_event(time(), 'hourly', 'retry_failed_order_requests');
	}

	
	return true;
}


add_action("wp","ifa_retry_failed_requests");
