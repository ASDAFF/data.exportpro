<?php
/**
 * TOP API: aliexpress.message.faq.del request
 * 
 * @author auto create
 * @since 1.0, 2018.07.25
 */
class AliexpressMessageFaqDelRequest
{
	/** 
	 * 参考
	 **/
	private $paramMessageFaqSubjectDto;
	
	private $apiParas = array();
	
	public function setParamMessageFaqSubjectDto($paramMessageFaqSubjectDto)
	{
		$this->paramMessageFaqSubjectDto = $paramMessageFaqSubjectDto;
		$this->apiParas["param_message_faq_subject_dto"] = $paramMessageFaqSubjectDto;
	}

	public function getParamMessageFaqSubjectDto()
	{
		return $this->paramMessageFaqSubjectDto;
	}

	public function getApiMethodName()
	{
		return "aliexpress.message.faq.del";
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
