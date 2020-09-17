<?php
/**
 * TOP API: aliexpress.solution.batch.product.price.update request
 * 
 * @author auto create
 * @since 1.0, 2019.12.11
 */
class AliexpressSolutionBatchProductPriceUpdateRequest
{
	/** 
	 * The product list, in which the price needs to be updated. Maximum length:20
	 **/
	private $mutipleProductUpdateList;
	
	private $apiParas = array();
	
	public function setMutipleProductUpdateList($mutipleProductUpdateList)
	{
		$this->mutipleProductUpdateList = $mutipleProductUpdateList;
		$this->apiParas["mutiple_product_update_list"] = $mutipleProductUpdateList;
	}

	public function getMutipleProductUpdateList()
	{
		return $this->mutipleProductUpdateList;
	}

	public function getApiMethodName()
	{
		return "aliexpress.solution.batch.product.price.update";
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
