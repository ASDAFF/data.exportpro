<?php

/**
 * 查询参数
 * @author auto create
 */
class AeopMarketingPromotionQuery
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
	 * 活动类型：店铺活动的限时限量折扣LimitedDiscount, 店铺自主满就减FixedDiscount,店铺自主全店铺打折StoreDiscount,店铺优惠券StoreCoupon,定向店铺优惠券DirectStoreCoupon,金币兑换店铺优惠券CoinsExchangeStoreCoupon,秒抢店铺优惠券GrabStoreCoupon,聚人气店铺优惠券PolyPopularityStoreCoupon, 购物券活动类型ShoppingCoupon,店铺自主满包邮FreeShipping，平台活动的限时限量折扣ProEngine,平台活动的全网大促销GPoint,平台活动的限时限量秒杀GAGA,平台活动的新品推介NewProduct,平台活动的团购GroupBuy
	 **/
	public $prom_type;
	
	/** 
	 * 发布中releasing, 发布已结束releasEnd, 冻结中\当前时间在 发布开始时间RELEASE_START_DATE减去24小时到发布开始时间RELEASE_START_DATE之间 frozen,新创建，当前时间在发布开始时间RELEASE_START_DATE减去24小时前的活动 created;招商未开始attractNotStart,招商中attracting,招商已结束attractEnd
	 **/
	public $status;	
}
?>