<?php

/**
 * 退件信息
 * @author auto create
 */
class ReturnerDto
{
	
	/** 
	 * 退件地址
	 **/
	public $address;
	
	/** 
	 * AE后台维护的退件地址ID
	 **/
	public $address_id;
	
	/** 
	 * 邮箱
	 **/
	public $email;
	
	/** 
	 * 移动电话, 校验格式：^1(3|4|5|6|7|8|9)\d{9}$
	 **/
	public $mobile;
	
	/** 
	 * 退件联系人名称，必须包含中文字符
	 **/
	public $name;
	
	/** 
	 * 固定电话，可空，校验格式：(^0[\d]{2,3}-[\d]{7,8}$)|(^400[\d]{3,4}[\d]{3,4}$)|(400-[\d]{3,4}-[\d]{3,4}$)
	 **/
	public $phone;	
}
?>