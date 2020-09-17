<?php
/**
 * TOP API: aliexpress.message.faq.list request
 * 
 * @author auto create
 * @since 1.0, 2018.07.25
 */
class AliexpressMessageFaqListRequest
{
	/** 
	 * 入参如下
	 **/
	private $paramMessageFaqQuery;
	
	private $apiParas = array();
	
	public function setParamMessageFaqQuery($paramMessageFaqQuery)
	{
		$this->paramMessageFaqQuery = $paramMessageFaqQuery;
		$this->apiParas["param_message_faq_query"] = $paramMessageFaqQuery;
	}

	public function getParamMessageFaqQuery()
	{
		return $this->paramMessageFaqQuery;
	}

	public function getApiMethodName()
	{
		return "aliexpress.message.faq.list";
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
