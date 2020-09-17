<?php

/**
 * result
 * @author auto create
 */
class AeopResultDto
{
	
	/** 
	 * 错误代码
	 **/
	public $error_code;
	
	/** 
	 * 创建失败时的错误信息
	 **/
	public $error_message;
	
	/** 
	 * 接口调用结果。true/false分别表示成功和失败。
	 **/
	public $success;
	
	/** 
	 * 新创建的产品组ID
	 **/
	public $target;
	
	/** 
	 * 创建产品分组的时间
	 **/
	public $time_stamp;	
}
?>