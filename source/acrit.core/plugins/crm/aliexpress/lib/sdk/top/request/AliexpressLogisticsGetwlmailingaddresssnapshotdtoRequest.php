<?php
/**
 * TOP API: aliexpress.logistics.getwlmailingaddresssnapshotdto request
 * 
 * @author auto create
 * @since 1.0, 2018.07.25
 */
class AliexpressLogisticsGetwlmailingaddresssnapshotdtoRequest
{
	/** 
	 * trade order id
	 **/
	private $tradeOrderId;
	
	private $apiParas = array();
	
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
		return "aliexpress.logistics.getwlmailingaddresssnapshotdto";
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
