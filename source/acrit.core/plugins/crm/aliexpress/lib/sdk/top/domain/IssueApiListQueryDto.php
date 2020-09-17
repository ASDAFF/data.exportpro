<?php

/**
 * 详情描述如下
 * @author auto create
 */
class IssueApiListQueryDto
{
	
	/** 
	 * 买家登录id
	 **/
	public $buyer_login_id;
	
	/** 
	 * 当前页
	 **/
	public $current_page;
	
	/** 
	 * 纠纷状态 处理中processing、 纠纷取消canceled_issue、纠纷完结,退款处理完成finish
	 **/
	public $issue_status;
	
	/** 
	 * 订单号
	 **/
	public $order_no;
	
	/** 
	 * 每页大小，不要大于50.
	 **/
	public $page_size;	
}
?>