<?php
/**
 * TOP API: aliexpress.issue.detail.get request
 * 
 * @author auto create
 * @since 1.0, 2019.10.30
 */
class AliexpressIssueDetailGetRequest
{
	/** 
	 * 买家登录帐号
	 **/
	private $buyerLoginId;
	
	/** 
	 * 纠纷id
	 **/
	private $issueId;
	
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

	public function getApiMethodName()
	{
		return "aliexpress.issue.detail.get";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->buyerLoginId,"buyerLoginId");
		RequestCheckUtil::checkNotNull($this->issueId,"issueId");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
