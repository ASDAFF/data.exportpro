<?php
/**
 * TOP API: aliexpress.marketing.storepromotions.querybyproduct request
 * 
 * @author auto create
 * @since 1.0, 2019.07.08
 */
class AliexpressMarketingStorepromotionsQuerybyproductRequest
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
		return "aliexpress.marketing.storepromotions.querybyproduct";
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
