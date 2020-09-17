<?php
/**
 * TOP API: aliexpress.issue.solution.save request
 * 
 * @author auto create
 * @since 1.0, 2018.07.30
 */
class AliexpressIssueSolutionSaveRequest
{
	/** 
	 * 详细参数如下
	 **/
	private $paramDto;
	
	private $apiParas = array();
	
	public function setParamDto($paramDto)
	{
		$this->paramDto = $paramDto;
		$this->apiParas["param_dto"] = $paramDto;
	}

	public function getParamDto()
	{
		return $this->paramDto;
	}

	public function getApiMethodName()
	{
		return "aliexpress.issue.solution.save";
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
