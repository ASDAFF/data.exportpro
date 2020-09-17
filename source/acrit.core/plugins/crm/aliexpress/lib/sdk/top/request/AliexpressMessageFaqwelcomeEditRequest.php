<?php
/**
 * TOP API: aliexpress.message.faqwelcome.edit request
 * 
 * @author auto create
 * @since 1.0, 2018.07.25
 */
class AliexpressMessageFaqwelcomeEditRequest
{
	/** 
	 * 详细解释如下：
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
		return "aliexpress.message.faqwelcome.edit";
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
