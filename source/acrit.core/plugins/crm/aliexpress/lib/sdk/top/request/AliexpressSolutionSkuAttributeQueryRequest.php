<?php
/**
 * TOP API: aliexpress.solution.sku.attribute.query request
 * 
 * @author auto create
 * @since 1.0, 2019.04.08
 */
class AliexpressSolutionSkuAttributeQueryRequest
{
	/** 
	 * input parameters
	 **/
	private $querySkuAttributeInfoRequest;
	
	private $apiParas = array();
	
	public function setQuerySkuAttributeInfoRequest($querySkuAttributeInfoRequest)
	{
		$this->querySkuAttributeInfoRequest = $querySkuAttributeInfoRequest;
		$this->apiParas["query_sku_attribute_info_request"] = $querySkuAttributeInfoRequest;
	}

	public function getQuerySkuAttributeInfoRequest()
	{
		return $this->querySkuAttributeInfoRequest;
	}

	public function getApiMethodName()
	{
		return "aliexpress.solution.sku.attribute.query";
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
