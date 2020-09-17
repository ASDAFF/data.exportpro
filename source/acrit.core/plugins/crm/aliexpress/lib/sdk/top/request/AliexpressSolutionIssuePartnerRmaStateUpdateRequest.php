<?php
/**
 * TOP API: aliexpress.solution.issue.partner.rma.state.update request
 * 
 * @author auto create
 * @since 1.0, 2019.01.03
 */
class AliexpressSolutionIssuePartnerRmaStateUpdateRequest
{
	/** 
	 * RMA's order state update request
	 **/
	private $rmaStateUpdateRequest;
	
	private $apiParas = array();
	
	public function setRmaStateUpdateRequest($rmaStateUpdateRequest)
	{
		$this->rmaStateUpdateRequest = $rmaStateUpdateRequest;
		$this->apiParas["rma_state_update_request"] = $rmaStateUpdateRequest;
	}

	public function getRmaStateUpdateRequest()
	{
		return $this->rmaStateUpdateRequest;
	}

	public function getApiMethodName()
	{
		return "aliexpress.solution.issue.partner.rma.state.update";
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
