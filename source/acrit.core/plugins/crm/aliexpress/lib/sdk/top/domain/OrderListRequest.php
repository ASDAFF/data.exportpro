<?php

/**
 * 入参
 * @author auto create
 */
class OrderListRequest
{
	
	/** 
	 * 订单创建时间结束值，格式: yyyy-MM-dd hh:MM:ss,如2015-07-10 00:00:00 倘若时间维度未精确到时分秒，故该时间条件筛选不许生效。此入参时间为美国太平洋时间。
	 **/
	public $create_date_end;
	
	/** 
	 * 订单创建时间起始值，格式: yyyy-MM-dd hh:MM:ss,如2015-07-09 00:00:00 倘若时间维度未精确到时分秒，故该时间条件筛选不许生效。此入参为美国太平洋时间。
	 **/
	public $create_date_start;
	
	/** 
	 * 订单状态： PLACE_ORDER_SUCCESS:等待买家付款; IN_CANCEL:买家申请取消; WAIT_SELLER_SEND_GOODS:等待您发货; SELLER_PART_SEND_GOODS:部分发货; WAIT_BUYER_ACCEPT_GOODS:等待买家收货; FUND_PROCESSING:买家确认收货后，等待退放款处理的状态; FINISH:已结束的订单; IN_ISSUE:含纠纷的订单; IN_FROZEN:冻结中的订单; WAIT_SELLER_EXAMINE_MONEY:等待您确认金额; RISK_CONTROL:订单处于风控24小时中，从买家在线支付完成后开始，持续24小时。
	 **/
	public $order_status;
	
	/** 
	 * 查询多个订单状态下的订单信息，具体订单状态见order_status详情。
	 **/
	public $order_status_list;
	
	/** 
	 * 当前页码
	 **/
	public $page;
	
	/** 
	 * 每页订单数，最大50
	 **/
	public $page_size;	
}
?>