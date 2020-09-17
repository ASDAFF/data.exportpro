<?php

/**
 * 操作记录
 * @author auto create
 */
class ApiIssueProcessDto
{
	
	/** 
	 * 操作类型 发起纠纷initiate取消纠纷，cancel买家取消纠纷, 买家同意方案buyer_accept,卖家同意方案seller_accept,买家拒绝方案buyer_refuse, 买家创建方案buyer_create_solution, 买家修改方案buyer_modify_solution,买家删除方案buyer_delete_solution,卖家创建方案seller_create_solution,卖家修改方案seller_modify_solution,卖家删除方案seller_delete_solution
	 **/
	public $action_type;
	
	/** 
	 * 图片附件
	 **/
	public $attachments;
	
	/** 
	 * 过程context,eg:buyer提起填写内容,seller拒绝填写内容
	 **/
	public $content;
	
	/** 
	 * 创建时间
	 **/
	public $gmt_create;
	
	/** 
	 * 是否有buyer视频(淘宝视频必须授权才能播放,目前不支持)
	 **/
	public $has_buyer_video;
	
	/** 
	 * 是否有seller视频(淘宝视频必须授权才能播放,目前不支持)
	 **/
	public $has_seller_video;
	
	/** 
	 * 过程id
	 **/
	public $id;
	
	/** 
	 * issueId
	 **/
	public $issue_id;
	
	/** 
	 * 是否收到货
	 **/
	public $receive_goods;
	
	/** 
	 * 操作人类型：seller卖家，buyer买家，waiter客服，system
	 **/
	public $submit_member_type;	
}
?>