<?php
/**
 * TOP API: aliexpress.solution.issue.partner.rma.screening.create request
 * 
 * @author auto create
 * @since 1.0, 2019.01.03
 */
class AliexpressSolutionIssuePartnerRmaScreeningCreateRequest
{
	/** 
	 * Screening result creation request
	 **/
	private $screeningResultCreationRequest;
	
	private $apiParas = array();
	
	public function setScreeningResultCreationRequest($screeningResultCreationRequest)
	{
		$this->screeningResultCreationRequest = $screeningResultCreationRequest;
		$this->apiParas["screening_result_creation_request"] = $screeningResultCreationRequest;
	}

	public function getScreeningResultCreationRequest()
	{
		return $this->screeningResultCreationRequest;
	}

	public function getApiMethodName()
	{
		return "aliexpress.solution.issue.partner.rma.screening.create";
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
