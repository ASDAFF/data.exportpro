<?php

/**
 * refund info
 * @author auto create
 */
class GlobalAeopTpRefundInfoDto
{
	
	/** 
	 * refund cash amount
	 **/
	public $refund_cash_amt;
	
	/** 
	 * refund coupon amount
	 **/
	public $refund_coupon_amt;
	
	/** 
	 * refund reason
	 **/
	public $refund_reason;
	
	/** 
	 * refund status: wait_refund, refund_ok, refund_cancel, close, refund_frozen
	 **/
	public $refund_status;
	
	/** 
	 * refund time
	 **/
	public $refund_time;
	
	/** 
	 * refund type
	 **/
	public $refund_type;	
}
?>