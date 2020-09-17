<?php
/**
 * TOP API: aliexpress.solution.product.edit request
 * 
 * @author auto create
 * @since 1.0, 2020.06.03
 */
class AliexpressSolutionProductEditRequest
{
	/** 
	 * input param
	 **/
	private $editProductRequest;
	
	private $apiParas = array();
	
	public function setEditProductRequest($editProductRequest)
	{
		$this->editProductRequest = $editProductRequest;
		$this->apiParas["edit_product_request"] = $editProductRequest;
	}

	public function getEditProductRequest()
	{
		return $this->editProductRequest;
	}

	public function getApiMethodName()
	{
		return "aliexpress.solution.product.edit";
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
