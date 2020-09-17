<?php
/**
 * TOP API: aliexpress.issue.issuelist.get request
 * 
 * @author auto create
 * @since 1.0, 2019.10.30
 */
class AliexpressIssueIssuelistGetRequest
{
	/** 
	 * 详情描述如下
	 **/
	private $queryDto;
	
	private $apiParas = array();
	
	public function setQueryDto($queryDto)
	{
		$this->queryDto = $queryDto;
		$this->apiParas["query_dto"] = $queryDto;
	}

	public function getQueryDto()
	{
		return $this->queryDto;
	}

	public function getApiMethodName()
	{
		return "aliexpress.issue.issuelist.get";
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
