<?php

/**
 * Result list after all the item_content,which were previously submitted through API:aliexpress.solution.feed.submit, have been executed , including both successful and unsuccessful items.
 * @author auto create
 */
class SingleItemResponseDto
{
	
	/** 
	 * Corresponding to the item_content_id defined by the seller when invoking the API: aliexpress.solution.feed.submit
	 **/
	public $item_content_id;
	
	/** 
	 * Execution result of each item
	 **/
	public $item_execution_result;	
}
?>