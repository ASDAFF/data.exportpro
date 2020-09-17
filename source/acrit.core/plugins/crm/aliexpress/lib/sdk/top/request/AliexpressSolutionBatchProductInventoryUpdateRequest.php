<?php
/**
 * TOP API: aliexpress.solution.batch.product.inventory.update request
 * 
 * @author auto create
 * @since 1.0, 2019.09.02
 */
class AliexpressSolutionBatchProductInventoryUpdateRequest
{
	/** 
	 * The product list, in which the inventory needs to be updated. Maximum 20 products.
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
		return "aliexpress.solution.batch.product.inventory.update";
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
