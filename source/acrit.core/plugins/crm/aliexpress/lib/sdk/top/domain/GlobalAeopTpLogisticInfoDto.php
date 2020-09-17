<?php

/**
 * logistics info
 * @author auto create
 */
class GlobalAeopTpLogisticInfoDto
{
	
	/** 
	 * received time
	 **/
	public $gmt_received;
	
	/** 
	 * send time
	 **/
	public $gmt_send;
	
	/** 
	 * to get logistics tracking information
	 **/
	public $have_tracking_info;
	
	/** 
	 * logistics tracking number
	 **/
	public $logistics_no;
	
	/** 
	 * logistics service show name
	 **/
	public $logistics_service_name;
	
	/** 
	 * logistics service name key
	 **/
	public $logistics_type_code;
	
	/** 
	 * receive status。(default:initial value; received:; not_received; suspected_received)
	 **/
	public $receive_status;
	
	/** 
	 * un-receive reason,such as Country does not match
	 **/
	public $recv_status_desc;
	
	/** 
	 * ship order id
	 **/
	public $ship_order_id;	
}
?>