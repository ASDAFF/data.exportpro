<?php

/**
 * request parameters to query
 * @author auto create
 */
class ItemListQuery
{
	
	/** 
	 * Current page of products to be needed. The default page is page 1.
	 **/
	public $current_page;
	
	/** 
	 * Product Ids which needs to be excluded
	 **/
	public $excepted_product_ids;
	
	/** 
	 * Search for products created before a specific time，yyyy-MM-dd hh:mm:ss
	 **/
	public $gmt_create_end;
	
	/** 
	 * Search for products created after a specific time, format: yyyy-MM-dd hh:mm:ss
	 **/
	public $gmt_create_start;
	
	/** 
	 * Search for products modified before a specific time，yyyy-MM-dd hh:mm:ss
	 **/
	public $gmt_modified_end;
	
	/** 
	 * Search for product modified after a specific time，yyyy-MM-dd hh:mm:ss
	 **/
	public $gmt_modified_start;
	
	/** 
	 * Search field by product groups. Enter product group id (groupId).
	 **/
	public $group_id;
	
	/** 
	 * Whether having national quotation. "y" for yes, "n" for no.
	 **/
	public $have_national_quote;
	
	/** 
	 * Search field by expiration date. For example, if the value for expiration date is 3, it means to query products to be offline within 3 days.
	 **/
	public $off_line_time;
	
	/** 
	 * Login ID of product owner
	 **/
	public $owner_member_id;
	
	/** 
	 * Number of products to be queried at each page. The input value must be less than 100, the default value of which is 20.
	 **/
	public $page_size;
	
	/** 
	 * product id
	 **/
	public $product_id;
	
	/** 
	 * onSelling	Product operation status. Currently, it is divided into 4 types with the following input parameters respectively: onSelling; offline; auditing; and editingRequired.
	 **/
	public $product_status_type;
	
	/** 
	 * merchant sku code
	 **/
	public $sku_code;
	
	/** 
	 * Fuzzy search field by product subject. It only supports half-width numbers in English with a length not more than 128.
	 **/
	public $subject;
	
	/** 
	 * Reasons for product offline: expire_offline; user_offline; violate_offline; punish_offline; and degrade_offline.
	 **/
	public $ws_display;	
}
?>