<?php
/**
 * TOP API: aliexpress.solution.merchant.profile.get request
 * 
 * @author auto create
 * @since 1.0, 2019.10.08
 */
class AliexpressSolutionMerchantProfileGetRequest
{
	
	private $apiParas = array();
	
	public function getApiMethodName()
	{
		return "aliexpress.solution.merchant.profile.get";
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
