<?php

/**
 * 出参
 * @author auto create
 */
class AeopPageResultDto
{
	
	/** 
	 * 页码
	 **/
	public $current_page;
	
	/** 
	 * 错误码
	 **/
	public $error_code;
	
	/** 
	 * 错误信息
	 **/
	public $error_message;
	
	/** 
	 * 每页条数
	 **/
	public $page_size;
	
	/** 
	 * 是否成功
	 **/
	public $success;
	
	/** 
	 * 出参如下
	 **/
	public $target_list;
	
	/** 
	 * timeStamp
	 **/
	public $time_stamp;
	
	/** 
	 * 总数量(SC订单不包含在结果中）
	 **/
	public $total_count;
	
	/** 
	 * 总页数
	 **/
	public $total_page;	
}
?>