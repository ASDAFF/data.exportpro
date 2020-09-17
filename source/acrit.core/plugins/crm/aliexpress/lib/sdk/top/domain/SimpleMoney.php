<?php

/**
 * 新订单金额，比order_amount更准确，考虑了卖家调价及COD费用。仅限于新订单（7.18-7.31期间创建的部分订单及8.1以后创建的所有订单）。
 * @author auto create
 */
class SimpleMoney
{
	
	/** 
	 * 金额
	 **/
	public $amount;
	
	/** 
	 * 币种
	 **/
	public $currency_code;	
}
?>