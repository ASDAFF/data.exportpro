<?php

/**
 * product list
 * @author auto create
 */
class ItemDisplayDto
{
	
	/** 
	 * Coupon end date, GMT+8
	 **/
	public $coupon_end_date;
	
	/** 
	 * Coupon start date, GMT+8
	 **/
	public $coupon_start_date;
	
	/** 
	 * currency code
	 **/
	public $currency_code;
	
	/** 
	 * freight template id
	 **/
	public $freight_template_id;
	
	/** 
	 * time that product was created
	 **/
	public $gmt_create;
	
	/** 
	 * time that product was modifed
	 **/
	public $gmt_modified;
	
	/** 
	 * group id
	 **/
	public $group_id;
	
	/** 
	 * product image urls
	 **/
	public $image_u_r_ls;
	
	/** 
	 * seller login id
	 **/
	public $owner_member_id;
	
	/** 
	 * seller member seq
	 **/
	public $owner_member_seq;
	
	/** 
	 * product id
	 **/
	public $product_id;
	
	/** 
	 * max price among all skus of the product
	 **/
	public $product_max_price;
	
	/** 
	 * min price among all skus of the product
	 **/
	public $product_min_price;
	
	/** 
	 * product src
	 **/
	public $src;
	
	/** 
	 * product tite
	 **/
	public $subject;
	
	/** 
	 * product offline reason
	 **/
	public $ws_display;
	
	/** 
	 * product offline time
	 **/
	public $ws_offline_date;	
}
?>