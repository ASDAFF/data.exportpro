<?php

/**
 * 消息详情列表
 * @author auto create
 */
class MessageDetail
{
	
	/** 
	 * 消息内容
	 **/
	public $content;
	
	/** 
	 * 扩展ID，如messageType=product, extern_id为productId,如messageType=order, extern_id为orderId;如messageType=member,则为空;对应summary中有相应的附属信息，如为product,则有产品相关的信息；如为order,则有订单相关信息
	 **/
	public $extern_id;
	
	/** 
	 * filePath
	 **/
	public $file_path_list;
	
	/** 
	 * 创建时间
	 **/
	public $gmt_create;
	
	/** 
	 * 消息ID
	 **/
	public $id;
	
	/** 
	 * 消息类别(product/order/member)
	 **/
	public $message_type;
	
	/** 
	 * 消息发送者ID
	 **/
	public $sender_ali_id;
	
	/** 
	 * 消息发送者名字
	 **/
	public $sender_name;
	
	/** 
	 * 摘要
	 **/
	public $summary;	
}
?>