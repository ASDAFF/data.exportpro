<?php

/**
 * 子订单列表
 * @author auto create
 */
class AeopTpChildOrderDto
{
	
	/** 
	 * 联盟佣金比例
	 **/
	public $afflicate_fee_rate;
	
	/** 
	 * 买家备注(子订单级别)
	 **/
	public $buyer_memo;
	
	/** 
	 * 纠纷信息
	 **/
	public $child_issue_info;
	
	/** 
	 * 子订单ID
	 **/
	public $child_order_id;
	
	/** 
	 * 交易佣金比例
	 **/
	public $escrow_fee_rate;
	
	/** 
	 * 冻结状态（NO_FROZEN:未冻结；IN_FROZEN:冻结中）
	 **/
	public $frozen_status;
	
	/** 
	 * 资金状态(NOT_PAY：待支付；PAY_SUCCESS:支付成功)
	 **/
	public $fund_status;
	
	/** 
	 * 备货时间
	 **/
	public $goods_prepare_time;
	
	/** 
	 * 订单ID
	 **/
	public $id;
	
	/** 
	 * 订单原始总金额
	 **/
	public $init_order_amt;
	
	/** 
	 * 纠纷状态
	 **/
	public $issue_status;
	
	/** 
	 * 子订单放款信息
	 **/
	public $loan_info;
	
	/** 
	 * 放款金额
	 **/
	public $logistics_amount;
	
	/** 
	 * 物流服务
	 **/
	public $logistics_service_name;
	
	/** 
	 * 物流类型，买家选择的物流方式
	 **/
	public $logistics_type;
	
	/** 
	 * cainiaoInternationalWarehouse表示是菜鸟认证海外仓发货的，这类订单（子订单）将由菜鸟系统下发海外仓系统，进行订单履行，商家ERP需进行过滤此类型的订单（子订单）。其他情况为空
	 **/
	public $logistics_warehouse_type;
	
	/** 
	 * 每个piece或lot对应多少个产品
	 **/
	public $lot_num;
	
	/** 
	 * 子订单序号，用于子订单发货，即sub_trade_order_index
	 **/
	public $order_sort_id;
	
	/** 
	 * 订单状态：PLACE_ORDER_SUCCESS:等待买家付款;  IN_CANCEL:买家申请取消;  WAIT_SELLER_SEND_GOODS:等待您发货;  SELLER_PART_SEND_GOODS:部分发货;  WAIT_BUYER_ACCEPT_GOODS:等待买家收货;  FUND_PROCESSING:买卖家达成一致，资金处理中；  IN_ISSUE:含纠纷中的订单;  IN_FROZEN:冻结中的订单;  WAIT_SELLER_EXAMINE_MONEY:等待您确认金额;  RISK_CONTROL:订单处于风控24小时中，从买家在线支付完成后开始，持续24小时。
	 **/
	public $order_status;
	
	/** 
	 * 商品扩展属性，skuid等
	 **/
	public $product_attributes;
	
	/** 
	 * 商品数量
	 **/
	public $product_count;
	
	/** 
	 * 商品ID
	 **/
	public $product_id;
	
	/** 
	 * 商品主图Url
	 **/
	public $product_img_url;
	
	/** 
	 * 商品名称
	 **/
	public $product_name;
	
	/** 
	 * 商品单价
	 **/
	public $product_price;
	
	/** 
	 * 快照Url
	 **/
	public $product_snap_url;
	
	/** 
	 * 商品规格，已废弃
	 **/
	public $product_standard;
	
	/** 
	 * 产品单位
	 **/
	public $product_unit;
	
	/** 
	 * 子订单退款信息
	 **/
	public $refund_info;
	
	/** 
	 * 发货类型"SELLER_SEND_GOODS": 卖家发货; "WAREHOUSE_SEND_GOODS":仓库发货
	 **/
	public $send_goods_operator;
	
	/** 
	 * SKU信息
	 **/
	public $sku_code;
	
	/** 
	 * 快照ID
	 **/
	public $snapshot_id;
	
	/** 
	 * 产品快照的图片路径
	 **/
	public $snapshot_small_photo_path;
	
	/** 
	 * 子订单中的各种标
	 **/
	public $tags;	
}
?>