<?php

/**
 * result
 * @author auto create
 */
class AeopModifyProductResponse
{
	
	/** 
	 * 错误代码
	 **/
	public $error_code;
	
	/** 
	 * 错误详情
	 **/
	public $error_details;
	
	/** 
	 * 错误信息
	 **/
	public $error_message;
	
	/** 
	 * 接口调用结果。成功为true，失败为false。
	 **/
	public $is_success;
	
	/** 
	 * 编辑成功次数。对于编辑商品来说，这个参数为1。
	 **/
	public $modify_count;
	
	/** 
	 * 商品的ID。
	 **/
	public $product_id;	
}
?>