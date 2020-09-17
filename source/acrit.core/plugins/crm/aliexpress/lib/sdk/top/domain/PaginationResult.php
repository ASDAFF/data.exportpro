<?php

/**
 * result
 * @author auto create
 */
class PaginationResult
{
	
	/** 
	 * current page
	 **/
	public $current_page;
	
	/** 
	 * error code
	 **/
	public $error_code;
	
	/** 
	 * error massage
	 **/
	public $error_message;
	
	/** 
	 * the number of each page
	 **/
	public $page_size;
	
	/** 
	 * success or not
	 **/
	public $success;
	
	/** 
	 * target list
	 **/
	public $target_list;
	
	/** 
	 * timeStamp
	 **/
	public $time_stamp;
	
	/** 
	 * total count(SC order is not include the result）
	 **/
	public $total_count;
	
	/** 
	 * total page
	 **/
	public $total_page;	
}
?>