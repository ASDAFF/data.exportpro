<?php
/**
 * TOP API: aliexpress.logistics.sellershipmentsupportsubtradeorder request
 * 
 * @author auto create
 * @since 1.0, 2018.07.25
 */
class AliexpressLogisticsSellershipmentsupportsubtradeorderRequest
{
	/** 
	 * 1
	 **/
	private $subTradeOrderList;
	
	/** 
	 * 主订单ID
	 **/
	private $tradeOrderId;
	
	private $apiParas = array();
	
	public function setSubTradeOrderList($subTradeOrderList)
	{
		$this->subTradeOrderList = $subTradeOrderList;
		$this->apiParas["sub_trade_order_list"] = $subTradeOrderList;
	}

	public function getSubTradeOrderList()
	{
		return $this->subTradeOrderList;
	}

	public function setTradeOrderId($tradeOrderId)
	{
		$this->tradeOrderId = $tradeOrderId;
		$this->apiParas["trade_order_id"] = $tradeOrderId;
	}

	public function getTradeOrderId()
	{
		return $this->tradeOrderId;
	}

	public function getApiMethodName()
	{
		return "aliexpress.logistics.sellershipmentsupportsubtradeorder";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->tradeOrderId,"tradeOrderId");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
