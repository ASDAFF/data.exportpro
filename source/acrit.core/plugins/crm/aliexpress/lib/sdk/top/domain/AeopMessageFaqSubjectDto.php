<?php

/**
 * target
 * @author auto create
 */
class AeopMessageFaqSubjectDto
{
	
	/** 
	 * 类目id
	 **/
	public $category_code;
	
	/** 
	 * 类目名称（包含父类目）
	 **/
	public $category_name;
	
	/** 
	 * 问题内容
	 **/
	public $contents_list;
	
	/** 
	 * 类型：common 通用、category 类目
	 **/
	public $faq_type;
	
	/** 
	 * 创建时间
	 **/
	public $gmt_create;
	
	/** 
	 * 最后修改时间
	 **/
	public $gmt_modified;
	
	/** 
	 * 问题id
	 **/
	public $id;
	
	/** 
	 * 卖家主帐号登录id
	 **/
	public $seller_admin_login_id;
	
	/** 
	 * 排序号，倒序
	 **/
	public $sort_num;
	
	/** 
	 * 状态: 已发布 online,未发布 offline
	 **/
	public $status;
	
	/** 
	 * 问题标题
	 **/
	public $subject;	
}
?>