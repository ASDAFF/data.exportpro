<?php

/**
 * product list
 * @author auto create
 */
class OrderProductDto
{
	
	/** 
	 * afflicate fee rate
	 **/
	public $afflicate_fee_rate;
	
	/** 
	 * buyer first name
	 **/
	public $buyer_signer_first_name;
	
	/** 
	 * buyer last name
	 **/
	public $buyer_signer_last_name;
	
	/** 
	 * Whether child orders can submit disputes
	 **/
	public $can_submit_issue;
	
	/** 
	 * child order id
	 **/
	public $child_id;
	
	/** 
	 * delivery time
	 **/
	public $delivery_time;
	
	/** 
	 * escrow fee rate
	 **/
	public $escrow_fee_rate;
	
	/** 
	 * Limited time
	 **/
	public $freight_commit_day;
	
	/** 
	 * fund status (NOT_PAY; PAY_SUCCESS; WAIT_SELLER_CHECK)
	 **/
	public $fund_status;
	
	/** 
	 * goods prepare days
	 **/
	public $goods_prepare_time;
	
	/** 
	 * issue mode
	 **/
	public $issue_mode;
	
	/** 
	 * issue status (NO_ISSUE; IN_ISSUE; END_ISSUE)
	 **/
	public $issue_status;
	
	/** 
	 * Logistics amount(sub-orders have no shipping costs, please ignore)
	 **/
	public $logistics_amount;
	
	/** 
	 * logistics service show name
	 **/
	public $logistics_service_name;
	
	/** 
	 * logistics service name( key)
	 **/
	public $logistics_type;
	
	/** 
	 * buyer memo
	 **/
	public $memo;
	
	/** 
	 * fake one compensate three flag
	 **/
	public $money_back3x;
	
	/** 
	 * order ID
	 **/
	public $order_id;
	
	/** 
	 * product count
	 **/
	public $product_count;
	
	/** 
	 * product id
	 **/
	public $product_id;
	
	/** 
	 * product main image url
	 **/
	public $product_img_url;
	
	/** 
	 * product name
	 **/
	public $product_name;
	
	/** 
	 * product snap Url
	 **/
	public $product_snap_url;
	
	/** 
	 * product standard
	 **/
	public $product_standard;
	
	/** 
	 * product unit
	 **/
	public $product_unit;
	
	/** 
	 * product unit price
	 **/
	public $product_unit_price;
	
	/** 
	 * Shipper type. "SELLER_SEND_GOODS": seller shipping; "WAREHOUSE_SEND_GOODS": warehouse delivery
	 **/
	public $send_goods_operator;
	
	/** 
	 * Last delivery time
	 **/
	public $send_goods_time;
	
	/** 
	 * order show status
	 **/
	public $show_status;
	
	/** 
	 * sku code
	 **/
	public $sku_code;
	
	/** 
	 * child order status
	 **/
	public $son_order_status;
	
	/** 
	 * total product amount
	 **/
	public $total_product_amount;	
}
?>