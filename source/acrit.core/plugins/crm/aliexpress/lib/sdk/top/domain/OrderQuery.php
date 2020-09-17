<?php

/**
 * param
 * @author auto create
 */
class OrderQuery
{
	
	/** 
	 * buyer login id
	 **/
	public $buyer_login_id;
	
	/** 
	 * create date end time.It's US pacific time
	 **/
	public $create_date_end;
	
	/** 
	 * create date start time.It's US pacific time
	 **/
	public $create_date_start;
	
	/** 
	 * the current page
	 **/
	public $current_page;
	
	/** 
	 * modified date end time.It's US pacific time
	 **/
	public $modified_date_end;
	
	/** 
	 * modified date start time.It's US pacific time
	 **/
	public $modified_date_start;
	
	/** 
	 * Order status: PLACE_ORDER_SUCCESS: Waiting for buyer to pay; IN_CANCEL: Buyer request cancellation; WAIT_SELLER_SEND_GOODS: Waiting for your shipment; SELLER_PART_SEND_GOODS: Partial delivery; WAIT_BUYER_ACCEPT_GOODS: Waiting for buyer to receive goods; FUND_PROCESSING: Buyers agree, funds processing; IN_ISSUE : Orders in disputes; IN_FROZEN: Orders in freeze; WAIT_SELLER_EXAMINE_MONEY: Waiting for your confirmation amount; RISK_CONTROL: Orders are in 24 hours of risk control, starting 24 hours after the buyer's online payment is completed. The above status query can be separately queried separately, and the order status information is not included in the order status. (FINISH, closed order status) FINISH: The completed order needs to be queried separately.
	 **/
	public $order_status;
	
	/** 
	 * Query order information in multiple order status. For specific order status, see order_status description.
	 **/
	public $order_status_list;
	
	/** 
	 * Number of pages per page
	 **/
	public $page_size;	
}
?>