<?php
/**
 * TOP API: aliexpress.marketing.limitdiscountpromotionproduct.edit request
 * 
 * @author auto create
 * @since 1.0, 2019.07.08
 */
class AliexpressMarketingLimitdiscountpromotionproductEditRequest
{
	/** 
	 * 详细参考如下
	 **/
	private $paramLimitedDiscProductInputDto;
	
	private $apiParas = array();
	
	public function setParamLimitedDiscProductInputDto($paramLimitedDiscProductInputDto)
	{
		$this->paramLimitedDiscProductInputDto = $paramLimitedDiscProductInputDto;
		$this->apiParas["param_limited_disc_product_input_dto"] = $paramLimitedDiscProductInputDto;
	}

	public function getParamLimitedDiscProductInputDto()
	{
		return $this->paramLimitedDiscProductInputDto;
	}

	public function getApiMethodName()
	{
		return "aliexpress.marketing.limitdiscountpromotionproduct.edit";
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
