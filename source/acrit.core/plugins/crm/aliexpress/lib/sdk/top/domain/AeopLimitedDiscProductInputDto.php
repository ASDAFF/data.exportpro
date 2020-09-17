<?php

/**
 * 详细参考如下
 * @author auto create
 */
class AeopLimitedDiscProductInputDto
{
	
	/** 
	 * 已存在的粉丝折扣 默认为0
	 **/
	public $exist_store_fans_discount;
	
	/** 
	 * 活动商品对象列表
	 **/
	public $product_objects;
	
	/** 
	 * 活动id
	 **/
	public $promotion_id;
	
	/** 
	 * 粉丝折扣,与exist_store_fans_discount不同值时，才会更新粉丝折扣
	 **/
	public $store_club_discount_rate;	
}
?>