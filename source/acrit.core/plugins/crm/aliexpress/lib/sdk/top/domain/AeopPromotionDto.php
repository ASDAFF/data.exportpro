<?php

/**
 * target
 * @author auto create
 */
class AeopPromotionDto
{
	
	/** 
	 * 活动展示场景
	 **/
	public $apply_scene;
	
	/** 
	 * 招商结束时间
	 **/
	public $attract_end_date;
	
	/** 
	 * 招商开始时间
	 **/
	public $attract_start_date;
	
	/** 
	 * 创建人登录账号
	 **/
	public $creator_login_id;
	
	/** 
	 * 活动信息描述
	 **/
	public $description;
	
	/** 
	 * 前台展示活动名称
	 **/
	public $display_name;
	
	/** 
	 * 支付时间限制
	 **/
	public $pay_time_limit;
	
	/** 
	 * 活动模式 店铺活动store, 平台活动platform
	 **/
	public $prom_mode;
	
	/** 
	 * 活动库存策略
	 **/
	public $prom_rule;
	
	/** 
	 * 活动ID
	 **/
	public $promotion_id;
	
	/** 
	 * 发布结束时间
	 **/
	public $release_end_date;
	
	/** 
	 * 发布开始时间
	 **/
	public $release_start_date;
	
	/** 
	 * 活动状态 发布中releasing, 发布已结束releasEnd, 冻结中，当前时间在 发布开始时间RELEASE_START_DATE减去24小时到发布开始时间RELEASE_START_DATE之间 frozen,新创建，当前时间在发布开始时间RELEASE_START_DATE减去24小时前的活动 created;
	 **/
	public $status;
	
	/** 
	 * 库存id
	 **/
	public $stock_channel_id;
	
	/** 
	 * 活动类型：店铺活动的限时限量折扣LimitedDiscount, 店铺自主满就减FixedDiscount,店铺自主全店铺打折StoreDiscount,店铺优惠券StoreCoupon,定向店铺优惠券DirectStoreCoupon,金币兑换店铺优惠券CoinsExchangeStoreCoupon,秒抢店铺优惠券GrabStoreCoupon,聚人气店铺优惠券PolyPopularityStoreCoupon, 购物券活动类型ShoppingCoupon,店铺自主满包邮FreeShipping，平台活动的限时限量折扣ProEngine,平台活动的全网大促销GPoint,平台活动的限时限量秒杀GAGA,平台活动的新品推介NewProduct,平台活动的团购GroupBuy
	 **/
	public $type;	
}
?>