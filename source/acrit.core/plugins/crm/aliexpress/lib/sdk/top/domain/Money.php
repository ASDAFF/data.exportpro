<?php

/**
 * 订单金额
 * @author auto create
 */
class Money
{
	
	/** 
	 * 金额
	 **/
	public $amount;
	
	/** 
	 * 最小货币单位（例如人民币：分）
	 **/
	public $cent;
	
	/** 
	 * 到最小货币单元的乘积因子（例如人民币：100）
	 **/
	public $cent_factor;
	
	/** 
	 * 货币描述
	 **/
	public $currency;
	
	/** 
	 * 币种
	 **/
	public $currency_code;	
}
?>