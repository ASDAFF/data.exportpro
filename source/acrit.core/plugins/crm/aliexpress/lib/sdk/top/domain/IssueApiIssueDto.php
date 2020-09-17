<?php

/**
 * dataList
 * @author auto create
 */
class IssueApiIssueDto
{
	
	/** 
	 * 买家登录id
	 **/
	public $buyer_login_id;
	
	/** 
	 * 创建时间
	 **/
	public $gmt_create;
	
	/** 
	 * 最后修改时间
	 **/
	public $gmt_modified;
	
	/** 
	 * 纠纷id
	 **/
	public $issue_id;
	
	/** 
	 * 纠纷状态 处理中 processing、 纠纷取消canceled_issue、纠纷完结,退款处理完成finish
	 **/
	public $issue_status;
	
	/** 
	 * 订单id
	 **/
	public $order_id;
	
	/** 
	 * 子订单id
	 **/
	public $parent_order_id;
	
	/** 
	 * 纠纷原因中文
	 **/
	public $reason_chinese;
	
	/** 
	 * 纠纷原因英文
	 **/
	public $reason_english;	
}
?>