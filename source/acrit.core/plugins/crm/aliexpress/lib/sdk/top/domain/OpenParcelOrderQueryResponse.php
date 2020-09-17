<?php

/**
 * 响应数据
 * @author auto create
 */
class OpenParcelOrderQueryResponse
{
	
	/** 
	 * 交接仓编码，快递揽收场景,大包交接目的地国际分拨
	 **/
	public $handover_warehouse_code;
	
	/** 
	 * 交接仓名称，快递揽收场景,大包交接目的地国际分拨
	 **/
	public $handover_warehouse_name;
	
	/** 
	 * 该小包是否已经组包
	 **/
	public $has_been_handover;	
}
?>