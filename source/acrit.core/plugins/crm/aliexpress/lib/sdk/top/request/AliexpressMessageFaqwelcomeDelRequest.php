<?php
/**
 * TOP API: aliexpress.message.faqwelcome.del request
 * 
 * @author auto create
 * @since 1.0, 2018.07.25
 */
class AliexpressMessageFaqwelcomeDelRequest
{
	/** 
	 * 无
	 **/
	private $paramMessageFaqWelcomeDto;
	
	private $apiParas = array();
	
	public function setParamMessageFaqWelcomeDto($paramMessageFaqWelcomeDto)
	{
		$this->paramMessageFaqWelcomeDto = $paramMessageFaqWelcomeDto;
		$this->apiParas["param_message_faq_welcome_dto"] = $paramMessageFaqWelcomeDto;
	}

	public function getParamMessageFaqWelcomeDto()
	{
		return $this->paramMessageFaqWelcomeDto;
	}

	public function getApiMethodName()
	{
		return "aliexpress.message.faqwelcome.del";
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
