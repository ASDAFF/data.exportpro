<?php

/**
 * data
 * @author auto create
 */
class GlobalAeopTpOrderDetailDto
{
	
	/** 
	 * buyer info
	 **/
	public $buyer_info;
	
	/** 
	 * buyer full name
	 **/
	public $buyer_signer_fullname;
	
	/** 
	 * buyer login id
	 **/
	public $buyerloginid;
	
	/** 
	 * child order ext info list
	 **/
	public $child_order_ext_info_list;
	
	/** 
	 * child order list
	 **/
	public $child_order_list;
	
	/** 
	 * cpf  number of order
	 **/
	public $cpf_number;
	
	/** 
	 * escrow fee (deprecated)
	 **/
	public $escrow_fee;
	
	/** 
	 * frozen status
	 **/
	public $frozen_status;
	
	/** 
	 * fund status
	 **/
	public $fund_status;
	
	/** 
	 * order creation time
	 **/
	public $gmt_create;
	
	/** 
	 * modified time, it's US pacific time
	 **/
	public $gmt_modified;
	
	/** 
	 * successful payment time
	 **/
	public $gmt_pay_success;
	
	/** 
	 * Order end time
	 **/
	public $gmt_trade_end;
	
	/** 
	 * order ID
	 **/
	public $id;
	
	/** 
	 * order amount
	 **/
	public $init_oder_amount;
	
	/** 
	 * phone order or not
	 **/
	public $is_phone;
	
	/** 
	 * issue info
	 **/
	public $issue_info;
	
	/** 
	 * issue status
	 **/
	public $issue_status;
	
	/** 
	 * loan info
	 **/
	public $loan_info;
	
	/** 
	 * loan status
	 **/
	public $loan_status;
	
	/** 
	 * logisitcs escrow fee rate(Deprecated)
	 **/
	public $logisitcs_escrow_fee_rate;
	
	/** 
	 * logistics info
	 **/
	public $logistic_info_list;
	
	/** 
	 * logistics amount
	 **/
	public $logistics_amount;
	
	/** 
	 * logistics status：NO_LOGISTICS 、 WAIT_SELLER_SEND_GOODS, SELLER_SEND_PART_GOODS, SELLER_SEND_GOODS, BUYER_ACCEPT_GOODS,NO_LOGISTICS
	 **/
	public $logistics_status;
	
	/** 
	 * buyer memo
	 **/
	public $memo;
	
	/** 
	 * operation details list
	 **/
	public $opr_log_dto_list;
	
	/** 
	 * order amount
	 **/
	public $order_amount;
	
	/** 
	 * order end reason
	 **/
	public $order_end_reason;
	
	/** 
	 * Order Message list(deprecated)
	 **/
	public $order_msg_list;
	
	/** 
	 * Order Status：PLACE_ORDER_SUCCESS;  IN_CANCEL;  WAIT_SELLER_SEND_GOODS;  SELLER_PART_SEND_GOODS;  WAIT_BUYER_ACCEPT_GOODS;  FUND_PROCESSING; IN_ISSUE;  IN_FROZEN;  WAIT_SELLER_EXAMINE_MONEY;  RISK_CONTROL.
	 **/
	public $order_status;
	
	/** 
	 * Current status expiration date
	 **/
	public $over_time_left;
	
	/** 
	 * order pay amount(settlemet currency)
	 **/
	public $pay_amount_by_settlement_cur;
	
	/** 
	 * payment type
	 **/
	public $payment_type;
	
	/** 
	 * receipt address
	 **/
	public $receipt_address;
	
	/** 
	 * refund info
	 **/
	public $refund_info;
	
	/** 
	 * seller operator ali login id
	 **/
	public $seller_operator_aliidloginid;
	
	/** 
	 * seller operator login ID
	 **/
	public $seller_operator_login_id;
	
	/** 
	 * Seller full name
	 **/
	public $seller_signer_fullname;
	
	/** 
	 * Payment settlement currency
	 **/
	public $settlement_currency;	
}
?>