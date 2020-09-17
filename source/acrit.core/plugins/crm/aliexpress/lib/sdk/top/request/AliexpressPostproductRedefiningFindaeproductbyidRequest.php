<?php
/**
 * TOP API: aliexpress.postproduct.redefining.findaeproductbyid request
 * 
 * @author auto create
 * @since 1.0, 2020.01.21
 */
class AliexpressPostproductRedefiningFindaeproductbyidRequest
{
	/** 
	 * 商品ID
	 **/
	private $productId;
	
	private $apiParas = array();
	
	public function setProductId($productId)
	{
		$this->productId = $productId;
		$this->apiParas["product_id"] = $productId;
	}

	public function getProductId()
	{
		return $this->productId;
	}

	public function getApiMethodName()
	{
		return "aliexpress.postproduct.redefining.findaeproductbyid";
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
