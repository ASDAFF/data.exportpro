<?php

/**
 * All the skus included in one product.
 * @author auto create
 */
class SkuInfoDto
{
	
	/** 
	 * discount price for the sku. discount_price should be cheaper than price.
	 **/
	public $discount_price;
	
	/** 
	 * inventory
	 **/
	public $inventory;
	
	/** 
	 * price
	 **/
	public $price;
	
	/** 
	 * sku attribute list. Some categories don't have sku attributes, then sku_attributes_list should be empty.
	 **/
	public $sku_attributes_list;
	
	/** 
	 * Code for merchant's sku, important reference to maintain the relationship between merchant and Aliexpress's system.
	 **/
	public $sku_code;	
}
?>