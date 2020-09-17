<?php

/**
 * 大包详情
 * @author auto create
 */
class OpenHandoverContentDetailResponse
{
	
	/** 
	 * 实际费用
	 **/
	public $actual_fee;
	
	/** 
	 * 实际重量
	 **/
	public $actual_weight;
	
	/** 
	 * 预估费用
	 **/
	public $estimate_fee;
	
	/** 
	 * 预估重量
	 **/
	public $estimate_weight;
	
	/** 
	 * 费用币种
	 **/
	public $fee_currency;
	
	/** 
	 * 费用单位
	 **/
	public $fee_unit;
	
	/** 
	 * 交接物关联的交接单状态code
	 **/
	public $handover_order_status;
	
	/** 
	 * 交接物关联的交接单状态名称
	 **/
	public $handover_order_status_name;
	
	/** 
	 * 交接物物流订单编号
	 **/
	public $order_code;
	
	/** 
	 * 大包关联的小包列表
	 **/
	public $parcel_order_list;
	
	/** 
	 * 交接物状态
	 **/
	public $status;
	
	/** 
	 * 交接物状态
	 **/
	public $status_name;
	
	/** 
	 * 交接物运单号
	 **/
	public $tracking_number;
	
	/** 
	 * 重量单位
	 **/
	public $weight_unit;	
}
?>