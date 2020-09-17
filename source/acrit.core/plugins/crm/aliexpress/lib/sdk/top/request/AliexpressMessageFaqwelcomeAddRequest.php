<?php
/**
 * TOP API: aliexpress.message.faqwelcome.add request
 * 
 * @author auto create
 * @since 1.0, 2018.07.25
 */
class AliexpressMessageFaqwelcomeAddRequest
{
	/** 
	 * 系统自动生成
	 **/
	private $paramList;
	
	private $apiParas = array();
	
	public function setParamList($paramList)
	{
		$this->paramList = $paramList;
		$this->apiParas["param_list"] = $paramList;
	}

	public function getParamList()
	{
		return $this->paramList;
	}

	public function getApiMethodName()
	{
		return "aliexpress.message.faqwelcome.add";
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
