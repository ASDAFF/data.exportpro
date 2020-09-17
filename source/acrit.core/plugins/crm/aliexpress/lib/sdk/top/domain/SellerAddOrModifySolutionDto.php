<?php

/**
 * 详细参数如下
 * @author auto create
 */
class SellerAddOrModifySolutionDto
{
	
	/** 
	 * 是否新增方案(true新增,false修改)
	 **/
	public $add_seller_solution;
	
	/** 
	 * 方案类型(SNAD才可更改类型)：退款refund,退货退款return_and_refund
	 **/
	public $add_solution_type;
	
	/** 
	 * 买家登录id
	 **/
	public $buyer_login_id;
	
	/** 
	 * 拒绝买家方案id，增加方案时必填
	 **/
	public $buyer_solution_id;
	
	/** 
	 * 纠纷id
	 **/
	public $issue_id;
	
	/** 
	 * 修改的卖家方案id，修改方案时必填
	 **/
	public $modify_seller_solution_id;
	
	/** 
	 * 新增or修改金额(元)
	 **/
	public $refund_amount;
	
	/** 
	 * 新增or修改金额的币种
	 **/
	public $refund_amount_currency;
	
	/** 
	 * 退货地址id，新增退货方案时必填
	 **/
	public $return_good_address_id;
	
	/** 
	 * 新增or修改理由说明
	 **/
	public $solution_context;	
}
?>