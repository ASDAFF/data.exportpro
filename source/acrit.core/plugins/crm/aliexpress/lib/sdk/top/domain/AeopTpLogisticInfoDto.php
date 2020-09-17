<?php

/**
 * 物流信息
 * @author auto create
 */
class AeopTpLogisticInfoDto
{
	
	/** 
	 * 妥投时间（此时间为美国太平洋时间）
	 **/
	public $gmt_received;
	
	/** 
	 * 发货时间（此时间为美国太平洋时间）
	 **/
	public $gmt_send;
	
	/** 
	 * 是否可获取物流追踪信息
	 **/
	public $have_tracking_info;
	
	/** 
	 * 物流追踪号
	 **/
	public $logistics_no;
	
	/** 
	 * 发货物流服务展示线路英文名称
	 **/
	public $logistics_service_name;
	
	/** 
	 * 物流服务名称key(与声明发货中的service_name相同)
	 **/
	public $logistics_type_code;
	
	/** 
	 * 投递状态。(default:初始值; received:已经妥投; not_received:没有妥投; suspected_received:疑似妥投)
	 **/
	public $receive_status;
	
	/** 
	 * 未妥投原因，如国家/地区不匹配
	 **/
	public $recv_status_desc;
	
	/** 
	 * 物流订单ID
	 **/
	public $ship_order_id;	
}
?>