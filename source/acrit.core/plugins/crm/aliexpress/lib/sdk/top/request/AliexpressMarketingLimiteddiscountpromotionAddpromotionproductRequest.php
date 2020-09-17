<?php
/**
 * TOP API: aliexpress.marketing.limiteddiscountpromotion.addpromotionproduct request
 * 
 * @author auto create
 * @since 1.0, 2019.07.08
 */
class AliexpressMarketingLimiteddiscountpromotionAddpromotionproductRequest
{
	/** 
	 * 详细参考如下
	 **/
	private $limitedDiscProductInputDto;
	
	private $apiParas = array();
	
	public function setLimitedDiscProductInputDto($limitedDiscProductInputDto)
	{
		$this->limitedDiscProductInputDto = $limitedDiscProductInputDto;
		$this->apiParas["limited_disc_product_input_dto"] = $limitedDiscProductInputDto;
	}

	public function getLimitedDiscProductInputDto()
	{
		return $this->limitedDiscProductInputDto;
	}

	public function getApiMethodName()
	{
		return "aliexpress.marketing.limiteddiscountpromotion.addpromotionproduct";
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
