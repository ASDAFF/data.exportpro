<?php

/**
 * 卖家协商方案
 * @author auto create
 */
class SolutionApiDto
{
	
	/** 
	 * 买家接受时间
	 **/
	public $buyer_accept_time;
	
	/** 
	 * 内容
	 **/
	public $content;
	
	/** 
	 * 方案创建时间
	 **/
	public $gmt_create;
	
	/** 
	 * 最后修改时间
	 **/
	public $gmt_modified;
	
	/** 
	 * 方案id
	 **/
	public $id;
	
	/** 
	 * 是否是默认方案
	 **/
	public $is_default;
	
	/** 
	 * 纠纷id
	 **/
	public $issue_id;
	
	/** 
	 * 退货运费金额
	 **/
	public $logistics_fee_amount;
	
	/** 
	 * 退货运费币种
	 **/
	public $logistics_fee_amount_currency;
	
	/** 
	 * 运费承担方：seller、buyer、platform
	 **/
	public $logistics_fee_bear_role;
	
	/** 
	 * 订单id
	 **/
	public $order_id;
	
	/** 
	 * 达成时间
	 **/
	public $reached_time;
	
	/** 
	 * 方案达成类型：协商一致negotiation_consensus、平台仲裁platform_arbitrate、卖家响应超时seller_reponse_timeout
	 **/
	public $reached_type;
	
	/** 
	 * 退款金额本币
	 **/
	public $refund_money;
	
	/** 
	 * 本币币种
	 **/
	public $refund_money_currency;
	
	/** 
	 * 退款金额 美金
	 **/
	public $refund_money_post;
	
	/** 
	 * refundMoneyPostCurrency
	 **/
	public $refund_money_post_currency;
	
	/** 
	 * 卖家接受时间
	 **/
	public $seller_accept_time;
	
	/** 
	 * 方案提出者
	 **/
	public $solution_owner;
	
	/** 
	 * 方案类型：退款refund、退货退款return_and_refund
	 **/
	public $solution_type;
	
	/** 
	 * 方案状态 待买卖家双方接受wait_buyer_and_seller_accept,待买家接受wait_buyer_accept,待卖家接受wait_seller_accept,达成reached,买家拒绝buyer_refused,卖家接受买家拒绝(针对平台方案)seller_accept_and_buyer_refused,退货阶段,卖家上升仲裁,平台给方案,之前达成的方案改成此状态reach_cancle,执行perform
	 **/
	public $status;
	
	/** 
	 * 版本号
	 **/
	public $version;	
}
?>