<?php
/**
 * TOP API: aliexpress.data.redefining.queryproductbusinessinfobyid request
 * 
 * @author auto create
 * @since 1.0, 2018.07.25
 */
class AliexpressDataRedefiningQueryproductbusinessinfobyidRequest
{
	/** 
	 * 商品id
	 **/
	private $paramString;
	
	private $apiParas = array();
	
	public function setParamString($paramString)
	{
		$this->paramString = $paramString;
		$this->apiParas["param_string"] = $paramString;
	}

	public function getParamString()
	{
		return $this->paramString;
	}

	public function getApiMethodName()
	{
		return "aliexpress.data.redefining.queryproductbusinessinfobyid";
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
