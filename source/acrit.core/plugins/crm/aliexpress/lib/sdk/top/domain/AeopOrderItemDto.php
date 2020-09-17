<?php

/**
 * 出参如下
 * @author auto create
 */
class AeopOrderItemDto
{
	
	/** 
	 * 订单类型。（AE_COMMON:普通订单;AE_TRIAL:试用订单;AE_RECHARGE:手机充值订单)
	 **/
	public $biz_type;
	
	/** 
	 * 买家登录id
	 **/
	public $buyer_login_id;
	
	/** 
	 * 买家全名
	 **/
	public $buyer_signer_fullname;
	
	/** 
	 * 结束原因
	 **/
	public $end_reason;
	
	/** 
	 * 手续费
	 **/
	public $escrow_fee;
	
	/** 
	 * 手续费率
	 **/
	public $escrow_fee_rate;
	
	/** 
	 * 冻结状态。(NO_FROZEN:无冻结; IN_FROZEN:冻结中)
	 **/
	public $frozen_status;
	
	/** 
	 * 资金状态。(NOT_PAY:未付款; PAY_SUCCESS:付款成功; WAIT_SELLER_CHECK:卖家验款)
	 **/
	public $fund_status;
	
	/** 
	 * 订单创建时间
	 **/
	public $gmt_create;
	
	/** 
	 * 订单支付时间（和订单详情中gmtPaysuccess字段意义相同。)
	 **/
	public $gmt_pay_time;
	
	/** 
	 * 最后一次订单发货时间（已完成订单不返回时间）
	 **/
	public $gmt_send_goods_time;
	
	/** 
	 * 订单最后更新时间
	 **/
	public $gmt_update;
	
	/** 
	 * 是否已请求放款
	 **/
	public $has_request_loan;
	
	/** 
	 * 纠纷状态。(NO_ISSUE:无纠纷; IN_ISSUE:纠纷中; END_ISSUE:纠纷结束)
	 **/
	public $issue_status;
	
	/** 
	 * 剩余发货时间（天）
	 **/
	public $left_send_good_day;
	
	/** 
	 * 剩余发货时间（小时）
	 **/
	public $left_send_good_hour;
	
	/** 
	 * 剩余发货时间（分钟）
	 **/
	public $left_send_good_min;
	
	/** 
	 * 放款金额
	 **/
	public $loan_amount;
	
	/** 
	 * 运费佣金比例
	 **/
	public $logisitcs_escrow_fee_rate;
	
	/** 
	 * 物流状态。（WAIT_SELLER_SEND_GOODS:等待卖家发货; SELLER_SEND_PART_GOODS:卖家部分发货; SELLER_SEND_GOODS:卖家已发货; BUYER_ACCEPT_GOODS:买家已确认收货; NO_LOGISTICS:没有物流流转信息）
	 **/
	public $logistics_status;
	
	/** 
	 * 订单详情链接
	 **/
	public $order_detail_url;
	
	/** 
	 * 订单ID
	 **/
	public $order_id;
	
	/** 
	 * 订单状态
	 **/
	public $order_status;
	
	/** 
	 * 付款金额
	 **/
	public $pay_amount;
	
	/** 
	 * 付款方式。 （migs:信用卡支付走人民币渠道; migs102:MasterCard支付并且走人民币渠道; migs101:Visa支付并且走人民币渠道; pp101:PayPal; mb: MoneyBooker渠道; tt101:Bank Transfer支付; wu101: West Union支付； wp101:Visa走美金渠道的支付; wp102:Mastercard走美金渠道的支付; qw101:QIWI支付; cybs101:Visa走CYBS渠道的支付; cybs102: Mastercard走CYBS渠道的支付; wm101:WebMoney支付; ebanx101:巴西Beloto支付）
	 **/
	public $payment_type;
	
	/** 
	 * 是否手机订单
	 **/
	public $phone;
	
	/** 
	 * 商品列表
	 **/
	public $product_list;
	
	/** 
	 * 卖家登录ID
	 **/
	public $seller_login_id;
	
	/** 
	 * 卖家操作人员的登录id
	 **/
	public $seller_operator_login_id;
	
	/** 
	 * 卖家全名
	 **/
	public $seller_signer_fullname;
	
	/** 
	 * 当前状态下的超时剩余时间（负数表示已超时时间）
	 **/
	public $timeout_left_time;	
}
?>