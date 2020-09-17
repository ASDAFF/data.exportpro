<?php

/**
 * 详细参考如下
 * @author auto create
 */
class AeopTpSingleOrderQuery
{
	
	/** 
	 * 扩展信息目前支持纠纷信息，放款信息，物流信息，买方信息和退款信息，分别对应二进制位1,2,3,4,5。例如，只查询纠纷信息和物流信息，extInfoBitFlag=10100；将此字段留空意味着返回所有信息。
	 **/
	public $ext_info_bit_flag;
	
	/** 
	 * 暂不支持.
	 **/
	public $field_list;
	
	/** 
	 * 订单id
	 **/
	public $order_id;
	
	/** 
	 * 暂不支持
	 **/
	public $show_id;	
}
?>