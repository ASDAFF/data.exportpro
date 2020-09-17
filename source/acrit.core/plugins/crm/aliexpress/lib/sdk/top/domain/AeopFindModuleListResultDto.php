<?php

/**
 * result
 * @author auto create
 */
class AeopFindModuleListResultDto
{
	
	/** 
	 * 模块信息列表
	 **/
	public $aeop_detail_module_list;
	
	/** 
	 * 当前页号
	 **/
	public $current_page;
	
	/** 
	 * 错误代码
	 **/
	public $error_code;
	
	/** 
	 * error_message
	 **/
	public $error_message;
	
	/** 
	 * 错误信息
	 **/
	public $error_msg;
	
	/** 
	 * 接口调用结果。true/false分别表示成功和失败。
	 **/
	public $success;
	
	/** 
	 * 总页数
	 **/
	public $total_page;	
}
?>