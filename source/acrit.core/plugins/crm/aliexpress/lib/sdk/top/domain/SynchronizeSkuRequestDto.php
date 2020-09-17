<?php

/**
 * The sku list, in which the inventory needs to be updated within the same product id
 * @author auto create
 */
class SynchronizeSkuRequestDto
{
	
	/** 
	 * discount_price of an sku. If not set, the discount_price will be erased.
	 **/
	public $discount_price;
	
	/** 
	 * price of an sku
	 **/
	public $price;
	
	/** 
	 * sku code
	 **/
	public $sku_code;	
}
?>