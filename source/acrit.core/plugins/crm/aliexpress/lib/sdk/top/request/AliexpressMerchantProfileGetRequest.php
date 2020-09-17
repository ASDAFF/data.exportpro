<?php
/**
 * TOP API: aliexpress.merchant.profile.get request
 * 
 * @author auto create
 * @since 1.0, 2019.04.10
 */
class AliexpressMerchantProfileGetRequest
{
	
	private $apiParas = array();
	
	public function getApiMethodName()
	{
		return "aliexpress.merchant.profile.get";
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
