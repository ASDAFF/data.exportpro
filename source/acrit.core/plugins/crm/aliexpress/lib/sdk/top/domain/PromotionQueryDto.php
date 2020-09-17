<?php

/**
 * 查询参数
 * @author auto create
 */
class PromotionQueryDto
{
	
	/** 
	 * 页码
	 **/
	public $current_page;
	
	/** 
	 * 页大小
	 **/
	public $page_size;
	
	/** 
	 * 活动ID
	 **/
	public $promotion_id;
	
	/** 
	 * 活动名称
	 **/
	public $promotion_name;
	
	/** 
	 * 活动类型：单品折扣:ProductDiscount;搭配套餐:BundleDeals;拼团:GroupBuy;满件折:OrderQuantityDiscount;满立减:OrderPriceDiscount;满包邮:FreeShipping;
	 **/
	public $promotion_type;
	
	/** 
	 * 活动状态：未开始:NotStarted;进行中:Ongoing;已暂停:Invalid;已结束:Finished;
	 **/
	public $status;	
}
?>