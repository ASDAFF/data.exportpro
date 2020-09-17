<?php
/**
 * TOP API: aliexpress.solution.issue.partner.rma.reverselogistic.trackinginfo.create request
 * 
 * @author auto create
 * @since 1.0, 2019.01.03
 */
class AliexpressSolutionIssuePartnerRmaReverselogisticTrackinginfoCreateRequest
{
	/** 
	 * Logistic's order creation request
	 **/
	private $logisticsOrderCreationRequest;
	
	private $apiParas = array();
	
	public function setLogisticsOrderCreationRequest($logisticsOrderCreationRequest)
	{
		$this->logisticsOrderCreationRequest = $logisticsOrderCreationRequest;
		$this->apiParas["logistics_order_creation_request"] = $logisticsOrderCreationRequest;
	}

	public function getLogisticsOrderCreationRequest()
	{
		return $this->logisticsOrderCreationRequest;
	}

	public function getApiMethodName()
	{
		return "aliexpress.solution.issue.partner.rma.reverselogistic.trackinginfo.create";
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
