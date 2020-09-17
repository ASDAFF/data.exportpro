<?php
/**
 * TOP API: aliexpress.merchant.oversea.brand.get request
 * 
 * @author auto create
 * @since 1.0, 2019.06.28
 */
class AliexpressMerchantOverseaBrandGetRequest
{
	
	private $apiParas = array();
	
	public function getApiMethodName()
	{
		return "aliexpress.merchant.oversea.brand.get";
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
