<?php

/**
 * 活动列表
 * @author auto create
 */
class SellerCouponActivity
{
	
	/** 
	 * coupon活动结束时间
	 **/
	public $acquire_end_date;
	
	/** 
	 * coupon活动开始时间
	 **/
	public $acquire_start_date;
	
	/** 
	 * coupon使用限制描述，json表示满多少减多少金额。(denomination：coupon面额(单位：美分); hasUseCondtion：是否有使用条件(y/n); minOrderAmount：coupon使用最小订单金额(单位：美分))
	 **/
	public $activity_desc;
	
	/** 
	 * 活动名称
	 **/
	public $activity_name;
	
	/** 
	 * 消费结束时间
	 **/
	public $consume_end_date;
	
	/** 
	 * 消费开始时间
	 **/
	public $consume_start_date;
	
	/** 
	 * coupon有效期，单位:秒
	 **/
	public $consume_valid_time;
	
	/** 
	 * coupon面额，单位:美分
	 **/
	public $denomination;
	
	/** 
	 * 扩展属性
	 **/
	public $ext_attrs;
	
	/** 
	 * 是否有使用条件
	 **/
	public $has_use_condtion;
	
	/** 
	 * coupon活动ID
	 **/
	public $id;
	
	/** 
	 * 订单使用最小金额，单位:美分
	 **/
	public $min_order_amount;
	
	/** 
	 * 每买家限领张数
	 **/
	public $num_per_buyer;
	
	/** 
	 * 定向类型
	 **/
	public $range_type;
	
	/** 
	 * 已发行数量
	 **/
	public $released_num;
	
	/** 
	 * 卖家主帐号seq
	 **/
	public $seller_admin_id;
	
	/** 
	 * 活动状态
	 **/
	public $status;
	
	/** 
	 * 总共发行数量
	 **/
	public $total_release_num;
	
	/** 
	 * 活动类型
	 **/
	public $type;	
}
?>