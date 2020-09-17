<?php
/**
 * TOP API: aliexpress.trade.seller.orderlist.get request
 * 
 * @author auto create
 * @since 1.0, 2020.03.12
 */
class AliexpressTradeSellerOrderlistGetRequest
{
	/** 
	 * 入参
	 **/
	private $paramAeopOrderQuery;
	
	private $apiParas = array();
	
	public function setParamAeopOrderQuery($paramAeopOrderQuery)
	{
		$this->paramAeopOrderQuery = $paramAeopOrderQuery;
		$this->apiParas["param_aeop_order_query"] = $paramAeopOrderQuery;
	}

	public function getParamAeopOrderQuery()
	{
		return $this->paramAeopOrderQuery;
	}

	public function getApiMethodName()
	{
		return "aliexpress.trade.seller.orderlist.get";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
