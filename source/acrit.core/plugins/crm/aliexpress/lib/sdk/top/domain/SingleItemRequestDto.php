<?php

/**
 * item list, maximum size: 2000.
 * @author auto create
 */
class SingleItemRequestDto
{
	
	/** 
	 * Content of each item, which follows different format according to different feed type.
	 **/
	public $item_content;
	
	/** 
	 * The id of the item_content, which could be defined by the seller. item_content_id should be unique among all the items in item_list.This field also appears in the API:aliexpress.solution.feed.query, which is regarding the convenience for the sellers to match the item_execuation_result with the item_content.
	 **/
	public $item_content_id;	
}
?>