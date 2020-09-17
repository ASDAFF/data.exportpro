<?php

/**
 * 订单列表
 * @author auto create
 */
class SimpleOrderItemVo
{
	
	/** 
	 * 订单类型（AE_COMMON:普通订单;AE_TRIAL:试用订单;AE_RECHARGE:手机充值订单)
	 **/
	public $biz_type;
	
	/** 
	 * 订单创建时间，美国太平洋时间
	 **/
	public $gmt_create;
	
	/** 
	 * 订单修改时间，美国太平洋时间
	 **/
	public $gmt_modified;
	
	/** 
	 * 订单备注
	 **/
	public $memo;
	
	/** 
	 * 订单ID
	 **/
	public $order_id;
	
	/** 
	 * 订单状态
	 **/
	public $order_status;
	
	/** 
	 * 商品列表
	 **/
	public $product_list;
	
	/** 
	 * 当前状态的超时剩余时间，单位毫秒（负数表示已超时时间）。已作废,不再返回值。
	 **/
	public $timeout_left_time;	
}
?>