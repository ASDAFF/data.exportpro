<?php

/**
 * The product list, in which the price needs to be updated. Maximum length:20
 * @author auto create
 */
class SynchronizeProductRequestDto
{
	
	/** 
	 * multi country price configuration
	 **/
	public $multi_country_price_configuration;
	
	/** 
	 * The sku list, in which the inventory needs to be updated within the same product id
	 **/
	public $multiple_sku_update_list;
	
	/** 
	 * product id
	 **/
	public $product_id;	
}
?>