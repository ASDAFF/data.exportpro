<?php

/**
 * 订单详情
 * @author auto create
 */
class AeopTpOrderDetailDto
{
	
	/** 
	 * 买家信息
	 **/
	public $buyer_info;
	
	/** 
	 * 买家全名
	 **/
	public $buyer_signer_fullname;
	
	/** 
	 * 买家登录id
	 **/
	public $buyerloginid;
	
	/** 
	 * 买家申请取消订单的原因，仅对取消中的订单有效
	 **/
	public $cancel_request_reason;
	
	/** 
	 * 商品信息
	 **/
	public $child_order_ext_info_list;
	
	/** 
	 * 子订单列表
	 **/
	public $child_order_list;
	
	/** 
	 * 是否是货到付款订单
	 **/
	public $cod;
	
	/** 
	 * 手续费（已废弃）
	 **/
	public $escrow_fee;
	
	/** 
	 * 冻结状态
	 **/
	public $frozen_status;
	
	/** 
	 * 资金状态
	 **/
	public $fund_status;
	
	/** 
	 * 订单创建时间(此时间为美国太平洋时间)
	 **/
	public $gmt_create;
	
	/** 
	 * 订单修改时间(此时间为美国太平洋时间)
	 **/
	public $gmt_modified;
	
	/** 
	 * 支付成功时间（与订单列表中gmtPayTime字段意义相同）(此时间为美国太平洋时间)
	 **/
	public $gmt_pay_success;
	
	/** 
	 * 订单结束时间(此时间为美国太平洋时间)
	 **/
	public $gmt_trade_end;
	
	/** 
	 * 订单ID
	 **/
	public $id;
	
	/** 
	 * 产品总金额
	 **/
	public $init_oder_amount;
	
	/** 
	 * 是否手机订单
	 **/
	public $is_phone;
	
	/** 
	 * 纠纷信息
	 **/
	public $issue_info;
	
	/** 
	 * 纠纷状态（IN_ISSUE:纠纷中，NO_ISSUE:无纠纷;END_ISSUE:纠纷结束）
	 **/
	public $issue_status;
	
	/** 
	 * 放款信息
	 **/
	public $loan_info;
	
	/** 
	 * 放款状态("loan_none":无放款;"wait_loan":等待放款;"loan_ok":放款成功)
	 **/
	public $loan_status;
	
	/** 
	 * 运费佣金比例(已废弃)
	 **/
	public $logisitcs_escrow_fee_rate;
	
	/** 
	 * 物流信息
	 **/
	public $logistic_info_list;
	
	/** 
	 * 物流费用
	 **/
	public $logistics_amount;
	
	/** 
	 * 物流状态：NO_LOGISTICS 无物流信息、等待卖家发货 WAIT_SELLER_SEND_GOODS,卖家部分发货 SELLER_SEND_PART_GOODS,卖家已发货  SELLER_SEND_GOODS,买家已确认收货  BUYER_ACCEPT_GOODS,NO_LOGISTICS
	 **/
	public $logistics_status;
	
	/** 
	 * 买家备注（订单级别）
	 **/
	public $memo;
	
	/** 
	 * 新订单金额，比order_amount更准确，考虑了卖家调价及COD费用。仅限于新订单（7.18-7.31期间创建的部分订单及8.1以后创建的所有订单）。
	 **/
	public $new_order_amount;
	
	/** 
	 * 操作日志列表
	 **/
	public $opr_log_dto_list;
	
	/** 
	 * 订单金额
	 **/
	public $order_amount;
	
	/** 
	 * 订单结束原因
	 **/
	public $order_end_reason;
	
	/** 
	 * 留言信息（已废弃）
	 **/
	public $order_msg_list;
	
	/** 
	 * 订单状态
	 **/
	public $order_status;
	
	/** 
	 * 当前状态超时日期 （此时间为美国太平洋时间）
	 **/
	public $over_time_left;
	
	/** 
	 * 买家支付金额(结算币种)
	 **/
	public $pay_amount_by_settlement_cur;
	
	/** 
	 * 付款方式 （migs信用卡支付走人民币渠道； migs102MasterCard支付并且走人民币渠道； migs101Visa支付并且走人民币渠道； pp101 PayPal； mb MoneyBooker渠道； tt101 Bank Transfer支付； wu101 West Union支付； wp101 Visa走美金渠道的支付； wp102 Mastercard 走美金渠道的支付； qw101 QIWI支付； cybs101 Visa走CYBS渠道的支付； cybs102 Mastercard 走CYBS渠道的支付； wm101 WebMoney支付； ebanx101 巴西Beloto支付 ；）
	 **/
	public $payment_type;
	
	/** 
	 * 收货地址信息
	 **/
	public $receipt_address;
	
	/** 
	 * 退款信息
	 **/
	public $refund_info;
	
	/** 
	 * 卖家操作员Ali id
	 **/
	public $seller_operator_aliidloginid;
	
	/** 
	 * 卖家操作员登录ID
	 **/
	public $seller_operator_login_id;
	
	/** 
	 * 卖家名称
	 **/
	public $seller_signer_fullname;
	
	/** 
	 * 支付金额结算币种
	 **/
	public $settlement_currency;	
}
?>