<?php

/**
 * supported sku attribute lis
 * @author auto create
 */
class SupportedSkuAttributeDto
{
	
	/** 
	 * aliexpress sku name, the same field when indicating the sku_name for posting product
	 **/
	public $aliexpress_sku_name;
	
	/** 
	 * aliexpress sku value list
	 **/
	public $aliexpress_sku_value_list;
	
	/** 
	 * Indicates whether this sku attribute is mandatory under this category
	 **/
	public $required;
	
	/** 
	 * whether the corresponding aliexpress_sku_name support customized name by merchants
	 **/
	public $support_customized_name;
	
	/** 
	 * whether the corresponding aliexpress_sku_name support customized picture
	 **/
	public $support_customized_picture;	
}
?>