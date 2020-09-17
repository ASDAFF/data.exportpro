<?php

/**
 * 返回结果集
 * @author auto create
 */
class IssueDetailOpenApiDto
{
	
	/** 
	 * 是否售后宝纠纷
	 **/
	public $after_sale_warranty;
	
	/** 
	 * 买家登录帐号
	 **/
	public $buyer_login_id;
	
	/** 
	 * 买家退货物流公司
	 **/
	public $buyer_return_logistics_company;
	
	/** 
	 * 退货物流订单LP单号
	 **/
	public $buyer_return_logistics_lp_no;
	
	/** 
	 * 买家退货单号
	 **/
	public $buyer_return_no;
	
	/** 
	 * 买家协商方案
	 **/
	public $buyer_solution_list;
	
	/** 
	 * 纠纷创建时间
	 **/
	public $gmt_create;
	
	/** 
	 * 纠纷id
	 **/
	public $id;
	
	/** 
	 * 纠纷原因
	 **/
	public $issue_reason;
	
	/** 
	 * 纠纷原因id
	 **/
	public $issue_reason_id;
	
	/** 
	 * 纠纷状态 处理中 processing、 纠纷取消canceled_issue、纠纷完结,退款处理完成finish
	 **/
	public $issue_status;
	
	/** 
	 * 订单id
	 **/
	public $order_id;
	
	/** 
	 * 父订单id
	 **/
	public $parent_order_id;
	
	/** 
	 * 平台方案集合
	 **/
	public $platform_solution_list;
	
	/** 
	 * 操作记录
	 **/
	public $process_dto_list;
	
	/** 
	 * 产品名称
	 **/
	public $product_name;
	
	/** 
	 * 产品价格
	 **/
	public $product_price;
	
	/** 
	 * 产品价格币种
	 **/
	public $product_price_currency;
	
	/** 
	 * 退款上限
	 **/
	public $refund_money_max;
	
	/** 
	 * 退款上限币种
	 **/
	public $refund_money_max_currency;
	
	/** 
	 * 退款上限当地货币
	 **/
	public $refund_money_max_local;
	
	/** 
	 * 退款上限当地货币币种
	 **/
	public $refund_money_max_local_currency;
	
	/** 
	 * 卖家协商方案
	 **/
	public $seller_solution_list;	
}
?>