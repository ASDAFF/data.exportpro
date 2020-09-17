<?php
/**
 * TOP API: aliexpress.solution.issue.partner.rma.reverselogistic.state.update request
 * 
 * @author auto create
 * @since 1.0, 2019.01.03
 */
class AliexpressSolutionIssuePartnerRmaReverselogisticStateUpdateRequest
{
	/** 
	 * Logistic order state update request
	 **/
	private $logisticOrderStateUpdateRequest;
	
	private $apiParas = array();
	
	public function setLogisticOrderStateUpdateRequest($logisticOrderStateUpdateRequest)
	{
		$this->logisticOrderStateUpdateRequest = $logisticOrderStateUpdateRequest;
		$this->apiParas["logistic_order_state_update_request"] = $logisticOrderStateUpdateRequest;
	}

	public function getLogisticOrderStateUpdateRequest()
	{
		return $this->logisticOrderStateUpdateRequest;
	}

	public function getApiMethodName()
	{
		return "aliexpress.solution.issue.partner.rma.reverselogistic.state.update";
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
