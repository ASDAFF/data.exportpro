<?php

/**
 * Order Message list(deprecated)
 * @author auto create
 */
class GlobalAeopTpOrderMsgDto
{
	
	/** 
	 * business order id
	 **/
	public $business_order_id;
	
	/** 
	 * meesage content
	 **/
	public $content;
	
	/** 
	 * order creation time
	 **/
	public $gmt_create;
	
	/** 
	 * order modification time
	 **/
	public $gmt_modified;
	
	/** 
	 * order id
	 **/
	public $id;
	
	/** 
	 * message sender ( buyer; seller)
	 **/
	public $poster;
	
	/** 
	 * receiver admin account seq
	 **/
	public $receiver_admin_seq;
	
	/** 
	 * receiver first name
	 **/
	public $receiver_first_name;
	
	/** 
	 * receiver last name
	 **/
	public $receiver_last_name;
	
	/** 
	 * receiver ID
	 **/
	public $receiver_login_id;
	
	/** 
	 * receiver account seq
	 **/
	public $receiver_seq;
	
	/** 
	 * sender admin account seq
	 **/
	public $sender_admin_seq;
	
	/** 
	 * sender first name
	 **/
	public $sender_first_name;
	
	/** 
	 * send last name
	 **/
	public $sender_last_name;
	
	/** 
	 * sender login ID
	 **/
	public $sender_login_id;
	
	/** 
	 * sender account seq
	 **/
	public $sender_seq;
	
	/** 
	 * message status
	 **/
	public $status;	
}
?>