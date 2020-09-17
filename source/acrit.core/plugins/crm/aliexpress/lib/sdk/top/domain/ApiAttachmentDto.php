<?php

/**
 * 图片附件
 * @author auto create
 */
class ApiAttachmentDto
{
	
	/** 
	 * 图片路径
	 **/
	public $file_path;
	
	/** 
	 * 创建时间
	 **/
	public $gmt_create;
	
	/** 
	 * 纠纷id
	 **/
	public $issue_id;
	
	/** 
	 * 过程id
	 **/
	public $issue_process_id;
	
	/** 
	 * 所属人:buyer\seller
	 **/
	public $owner;	
}
?>