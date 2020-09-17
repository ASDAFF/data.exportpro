<?php

/**
 * 出参集合
 * @author auto create
 */
class PromotionSimpleInfoDto
{
	
	/** 
	 * 活动结束时间
	 **/
	public $end_time;
	
	/** 
	 * 活动ID
	 **/
	public $promotion_id;
	
	/** 
	 * 活动名称
	 **/
	public $promotion_name;
	
	/** 
	 * 活动开始时间
	 **/
	public $start_time;
	
	/** 
	 * 活动状态：未开始:NotStarted;进行中:Ongoing;已暂停:Invalid;已结束:Finished;
	 **/
	public $status;	
}
?>