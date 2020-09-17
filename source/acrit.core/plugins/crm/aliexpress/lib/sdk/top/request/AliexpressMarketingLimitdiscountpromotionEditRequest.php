<?php
/**
 * TOP API: aliexpress.marketing.limitdiscountpromotion.edit request
 * 
 * @author auto create
 * @since 1.0, 2018.07.25
 */
class AliexpressMarketingLimitdiscountpromotionEditRequest
{
	/** 
	 * 输入参数
	 **/
	private $paramLimitedDiscInputDto;
	
	private $apiParas = array();
	
	public function setParamLimitedDiscInputDto($paramLimitedDiscInputDto)
	{
		$this->paramLimitedDiscInputDto = $paramLimitedDiscInputDto;
		$this->apiParas["param_limited_disc_input_dto"] = $paramLimitedDiscInputDto;
	}

	public function getParamLimitedDiscInputDto()
	{
		return $this->paramLimitedDiscInputDto;
	}

	public function getApiMethodName()
	{
		return "aliexpress.marketing.limitdiscountpromotion.edit";
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
