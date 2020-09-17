<?php

/**
 * 商品sku信息
 * @author auto create
 */
class AeopSkuInventoryInfo
{
	
	/** 
	 * 限时限量更改为共享库存，当前该字段仅当部分sku不参加活动时设置为0
	 **/
	public $quantity;
	
	/** 
	 * sku属性
	 **/
	public $sku_attr;	
}
?>