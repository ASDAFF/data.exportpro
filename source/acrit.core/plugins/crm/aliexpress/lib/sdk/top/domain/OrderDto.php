<?php

/**
 * target list
 * @author auto create
 */
class OrderDto
{
	
	/** 
	 * order type。（AE_COMMON:common type,AE_TRIAL:trial type;AE_RECHARGE:recharge order)
	 **/
	public $biz_type;
	
	/** 
	 * buyer login id
	 **/
	public $buyer_login_id;
	
	/** 
	 * buyer full name
	 **/
	public $buyer_signer_fullname;
	
	/** 
	 * order finished reason
	 **/
	public $end_reason;
	
	/** 
	 * escrow fee
	 **/
	public $escrow_fee;
	
	/** 
	 * escrow fee rate
	 **/
	public $escrow_fee_rate;
	
	/** 
	 * frozen status。(NO_FROZEN:no frozen; IN_FROZEN:in frozen)
	 **/
	public $frozen_status;
	
	/** 
	 * fund status (NOT_PAY; PAY_SUCCESS; WAIT_SELLER_CHECK)
	 **/
	public $fund_status;
	
	/** 
	 * order create time,it's US Pacific time
	 **/
	public $gmt_create;
	
	/** 
	 * order pay time (The gmtPaysuccess field has the same meaning in the order details.)it's US Pacific time
	 **/
	public $gmt_pay_time;
	
	/** 
	 * Last order delivery time
	 **/
	public $gmt_send_goods_time;
	
	/** 
	 * Last order update time
	 **/
	public $gmt_update;
	
	/** 
	 * Have you requested a loan?
	 **/
	public $has_request_loan;
	
	/** 
	 * issue status (NO_ISSUE; IN_ISSUE; END_ISSUE)
	 **/
	public $issue_status;
	
	/** 
	 * Remaining delivery time (days)
	 **/
	public $left_send_good_day;
	
	/** 
	 * Remaining delivery time (hour）
	 **/
	public $left_send_good_hour;
	
	/** 
	 * Remaining delivery time (minute)
	 **/
	public $left_send_good_min;
	
	/** 
	 * loan amount details
	 **/
	public $loan_amount;
	
	/** 
	 * logistics escrow fee rate
	 **/
	public $logisitcs_escrow_fee_rate;
	
	/** 
	 * logistics status。logistics status。(WAIT_SELLER_SEND_GOODS: Waiting for seller to ship; SELLER_SEND_PART_GOODS: Partial delivery by seller; SELLER_SEND_GOODS: Seller has shipped; BUYER_ACCEPT_GOODS: Buyer has confirmed receipt; NO_LOGISTICS: No logistics transfer)
	 **/
	public $logistics_status;
	
	/** 
	 * order detail url
	 **/
	public $order_detail_url;
	
	/** 
	 * order ID
	 **/
	public $order_id;
	
	/** 
	 * order status
	 **/
	public $order_status;
	
	/** 
	 * pay amount
	 **/
	public $pay_amount;
	
	/** 
	 * pay type: migs: Credit card payments go through the RMB channel; migs: 102MasterCard pays and takes the RMB channel; migs101:Visa Pay and take the RMB channel; pp101: PayPal; mb: MoneyBooker channel; tt101: Bank Transfer payment; wu101: West Union payment; wp101: Visa pays for the US dollar channel; wp102: Mastercard to pay for the US dollar channel; qw101: QIWI payment; cybs101: Visa takes the payment of the CYBS channel; cybs102: Mastercard to pay for CYBS channels; wm101: WebMoney payment; ebanx101: Brazilian Beloto payment;
	 **/
	public $payment_type;
	
	/** 
	 * Whether mobile phone orders
	 **/
	public $phone;
	
	/** 
	 * product list
	 **/
	public $product_list;
	
	/** 
	 * seller login id
	 **/
	public $seller_login_id;
	
	/** 
	 * seller operator login id
	 **/
	public $seller_operator_login_id;
	
	/** 
	 * seller fuller name
	 **/
	public $seller_signer_fullname;
	
	/** 
	 * The remain time of the current status (negative number indicates the timeout period)
	 **/
	public $timeout_left_time;	
}
?>