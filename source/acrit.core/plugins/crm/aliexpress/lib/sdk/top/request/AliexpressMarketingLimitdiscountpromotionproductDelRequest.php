<?php
/**
 * TOP API: aliexpress.marketing.limitdiscountpromotionproduct.del request
 * 
 * @author auto create
 * @since 1.0, 2019.09.02
 */
class AliexpressMarketingLimitdiscountpromotionproductDelRequest
{
	/** 
	 * 系统自动生成
	 **/
	private $paramAeopLimitedDiscProductIdDTO;
	
	private $apiParas = array();
	
	public function setParamAeopLimitedDiscProductIdDTO($paramAeopLimitedDiscProductIdDTO)
	{
		$this->paramAeopLimitedDiscProductIdDTO = $paramAeopLimitedDiscProductIdDTO;
		$this->apiParas["param_aeop_limited_disc_product_id_d_t_o"] = $paramAeopLimitedDiscProductIdDTO;
	}

	public function getParamAeopLimitedDiscProductIdDTO()
	{
		return $this->paramAeopLimitedDiscProductIdDTO;
	}

	public function getApiMethodName()
	{
		return "aliexpress.marketing.limitdiscountpromotionproduct.del";
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
