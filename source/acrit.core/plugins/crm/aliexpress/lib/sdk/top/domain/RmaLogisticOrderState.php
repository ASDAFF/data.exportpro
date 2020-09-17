<?php

/**
 * Logistic order state information
 * @author auto create
 */
class RmaLogisticOrderState
{
	
	/** 
	 * Logistic order detail
	 **/
	public $order_state_detail;
	
	/** 
	 * values CANCELLED, PRODUCT_CAPTURED, INCIDENT, PRODUCT_DELIVERED
	 **/
	public $state;
	
	/** 
	 * State date. PST time
	 **/
	public $state_date;	
}
?>