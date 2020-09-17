<?php
/**
 * TOP API: aliexpress.logistics.redefining.getallprovince request
 * 
 * @author auto create
 * @since 1.0, 2018.07.25
 */
class AliexpressLogisticsRedefiningGetallprovinceRequest
{
	
	private $apiParas = array();
	
	public function getApiMethodName()
	{
		return "aliexpress.logistics.redefining.getallprovince";
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
