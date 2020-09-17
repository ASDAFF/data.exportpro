<?php
/**
 * TOP API: aliexpress.evaluation.listorderevaluation.get request
 * 
 * @author auto create
 * @since 1.0, 2018.07.25
 */
class AliexpressEvaluationListorderevaluationGetRequest
{
	/** 
	 * 详细参考如下
	 **/
	private $tradeEvaluationRequest;
	
	private $apiParas = array();
	
	public function setTradeEvaluationRequest($tradeEvaluationRequest)
	{
		$this->tradeEvaluationRequest = $tradeEvaluationRequest;
		$this->apiParas["trade_evaluation_request"] = $tradeEvaluationRequest;
	}

	public function getTradeEvaluationRequest()
	{
		return $this->tradeEvaluationRequest;
	}

	public function getApiMethodName()
	{
		return "aliexpress.evaluation.listorderevaluation.get";
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
