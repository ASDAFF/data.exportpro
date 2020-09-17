<?php
/**
 * TOP API: aliexpress.product.productgroups.get request
 * 
 * @author auto create
 * @since 1.0, 2018.07.25
 */
class AliexpressProductProductgroupsGetRequest
{
	
	private $apiParas = array();
	
	public function getApiMethodName()
	{
		return "aliexpress.product.productgroups.get";
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
