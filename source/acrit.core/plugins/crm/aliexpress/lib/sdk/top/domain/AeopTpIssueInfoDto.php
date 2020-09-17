<?php

/**
 * 纠纷信息
 * @author auto create
 */
class AeopTpIssueInfoDto
{
	
	/** 
	 * 纠纷类型
	 **/
	public $issue_model;
	
	/** 
	 * 纠纷状态： 处理中 processing、 纠纷取消canceled_issue、纠纷完结,退款处理完成finish
	 **/
	public $issue_status;
	
	/** 
	 * 纠纷创建时间(此时间为美国天平洋时间）
	 **/
	public $issue_time;	
}
?>