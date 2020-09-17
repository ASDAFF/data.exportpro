<?php

/**
 * Logistic's order creation request
 * @author auto create
 */
class LogisticOrderCreationForRmaRequest
{
	
	/** 
	 * Carrier name
	 **/
	public $carrier_name;
	
	/** 
	 * The dispute Id
	 **/
	public $dispute_id;
	
	/** 
	 * Values: PRODUCT_CUSTOMER_GATHERING,PRODUCT_RETURN_CUSTOMER,PRODUCT_RETURN_WAREHOUSE,PRODUCT_RETURN_SUPPLIER
	 **/
	public $logistic_reason;
	
	/** 
	 * Order date. PST time
	 **/
	public $order_date;
	
	/** 
	 * Carrier tracking code. Tracking code or Shipping code must be provided
	 **/
	public $tracking_code;	
}
?>