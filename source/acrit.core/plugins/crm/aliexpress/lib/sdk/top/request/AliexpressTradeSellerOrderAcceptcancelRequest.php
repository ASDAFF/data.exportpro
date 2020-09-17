<?php
/**
 * TOP API: aliexpress.trade.seller.order.acceptcancel request
 * 
 * @author auto create
 * @since 1.0, 2019.07.01
 */
class AliexpressTradeSellerOrderAcceptcancelRequest
{
	/** 
	 * 入参如下
	 **/
	private $paramOrderCancelRequest;
	
	private $apiParas = array();
	
	public function setParamOrderCancelRequest($paramOrderCancelRequest)
	{
		$this->paramOrderCancelRequest = $paramOrderCancelRequest;
		$this->apiParas["param_order_cancel_request"] = $paramOrderCancelRequest;
	}

	public function getParamOrderCancelRequest()
	{
		return $this->paramOrderCancelRequest;
	}

	public function getApiMethodName()
	{
		return "aliexpress.trade.seller.order.acceptcancel";
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
