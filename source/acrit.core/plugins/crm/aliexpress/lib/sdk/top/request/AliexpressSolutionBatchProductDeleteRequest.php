<?php
/**
 * TOP API: aliexpress.solution.batch.product.delete request
 * 
 * @author auto create
 * @since 1.0, 2020.02.11
 */
class AliexpressSolutionBatchProductDeleteRequest
{
	/** 
	 * maximum 100
	 **/
	private $productIds;
	
	private $apiParas = array();
	
	public function setProductIds($productIds)
	{
		$this->productIds = $productIds;
		$this->apiParas["product_ids"] = $productIds;
	}

	public function getProductIds()
	{
		return $this->productIds;
	}

	public function getApiMethodName()
	{
		return "aliexpress.solution.batch.product.delete";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkMaxListSize($this->productIds,100,"productIds");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
