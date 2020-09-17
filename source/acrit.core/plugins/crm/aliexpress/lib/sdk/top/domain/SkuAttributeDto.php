<?php

/**
 * sku attribute list. Some categories don't have sku attributes, then sku_attributes_list should be empty.
 * @author auto create
 */
class SkuAttributeDto
{
	
	/** 
	 * To obtain the available sku attribute names under a specific category, please check API: aliexpress.solution.sku.attribute.query
	 **/
	public $sku_attribute_name;
	
	/** 
	 * sku attribute value
	 **/
	public $sku_attribute_value;
	
	/** 
	 * The url needs to be accessible. The url could be located in the merchant's server or obtained by uploading the pictures to merchant's Aliexpress photobank, by using the API: aliexpress.photobank.redefining.uploadimageforsdk
	 **/
	public $sku_image;	
}
?>