<?php

/**
 * There are 4 types of how to fill in the content of each element in this attribute list. 1). When filling in the standard dropdown/multi-dropdown attributes, fill in "aliexpress_attribute_name_id" and "aliexpress_attribute_value_id"(For multi-dropdown, splitting them into multiple elements) 2). When filling in the attributes in which the value needs to be manually entered, fill in "aliexpress_attribute_name_id" and "attribute_value" in the element. 3). There exists a special kind of "aliexpress_attribute_value_id" of which the value represents for "Other". When encoutering this scenario, please fill in "aliexpress_attribute_name_id", "aliexpress_attribute_value_id" and "attribute_value". 4). Besides the three types mentioned above, if the seller would like to fully customized all the atttributes, fill in "attribute name" and "attribute_value" in the element.
 * @author auto create
 */
class AttributeDto
{
	
	/** 
	 * aliexpress attribute name id, which could be obtained from aliexpress.solution.sku.attribute.query
	 **/
	public $aliexpress_attribute_name_id;
	
	/** 
	 * aliexpress attribute value id, which could be obtained from aliexpress.solution.sku.attribute.query
	 **/
	public $aliexpress_attribute_value_id;
	
	/** 
	 * merchant's attribute name
	 **/
	public $attribute_name;
	
	/** 
	 * merchant's attribute value
	 **/
	public $attribute_value;	
}
?>