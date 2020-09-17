<?php
/**
 * TOP API: aliexpress.postproduct.redefining.claimtaobaoproductsapi request
 * 
 * @author auto create
 * @since 1.0, 2018.07.25
 */
class AliexpressPostproductRedefiningClaimtaobaoproductsapiRequest
{
	/** 
	 * 淘宝或者天猫产品的detail url，url需做代码转译。
	 **/
	private $url;
	
	private $apiParas = array();
	
	public function setUrl($url)
	{
		$this->url = $url;
		$this->apiParas["url"] = $url;
	}

	public function getUrl()
	{
		return $this->url;
	}

	public function getApiMethodName()
	{
		return "aliexpress.postproduct.redefining.claimtaobaoproductsapi";
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
