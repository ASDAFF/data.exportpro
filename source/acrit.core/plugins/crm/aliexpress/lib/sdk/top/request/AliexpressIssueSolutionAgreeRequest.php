<?php
/**
 * TOP API: aliexpress.issue.solution.agree request
 * 
 * @author auto create
 * @since 1.0, 2018.07.25
 */
class AliexpressIssueSolutionAgreeRequest
{
	/** 
	 * 买家登录id
	 **/
	private $buyerLoginId;
	
	/** 
	 * 纠纷id
	 **/
	private $issueId;
	
	/** 
	 * 若退货需提供退货地址id
	 **/
	private $returnAddressId;
	
	/** 
	 * 同意方案id
	 **/
	private $solutionId;
	
	private $apiParas = array();
	
	public function setBuyerLoginId($buyerLoginId)
	{
		$this->buyerLoginId = $buyerLoginId;
		$this->apiParas["buyer_login_id"] = $buyerLoginId;
	}

	public function getBuyerLoginId()
	{
		return $this->buyerLoginId;
	}

	public function setIssueId($issueId)
	{
		$this->issueId = $issueId;
		$this->apiParas["issue_id"] = $issueId;
	}

	public function getIssueId()
	{
		return $this->issueId;
	}

	public function setReturnAddressId($returnAddressId)
	{
		$this->returnAddressId = $returnAddressId;
		$this->apiParas["return_address_id"] = $returnAddressId;
	}

	public function getReturnAddressId()
	{
		return $this->returnAddressId;
	}

	public function setSolutionId($solutionId)
	{
		$this->solutionId = $solutionId;
		$this->apiParas["solution_id"] = $solutionId;
	}

	public function getSolutionId()
	{
		return $this->solutionId;
	}

	public function getApiMethodName()
	{
		return "aliexpress.issue.solution.agree";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->buyerLoginId,"buyerLoginId");
		RequestCheckUtil::checkNotNull($this->issueId,"issueId");
		RequestCheckUtil::checkNotNull($this->solutionId,"solutionId");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
