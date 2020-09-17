<?php

/**
 * RMA's state information
 * @author auto create
 */
class RmaState
{
	
	/** 
	 * Values: CANCELLED, PRODUCT_COLLECTED, PRODUCT_RECEIVED, PRODUCT_SCREENING, WAITING_AE_ACTION, COMPLETED, CANCELLED_LOGISTICS_ISSUE, CANCELLED_LOGISTICS_ISSUE_RETRIES
	 **/
	public $state;
	
	/** 
	 * Order data. PST time
	 **/
	public $state_date;
	
	/** 
	 * Detail of the state changed
	 **/
	public $state_detail;	
}
?>