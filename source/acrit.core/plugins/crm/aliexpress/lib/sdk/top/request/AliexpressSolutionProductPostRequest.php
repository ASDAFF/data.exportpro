<?php
/**
 * TOP API: aliexpress.solution.product.post request
 * 
 * @author auto create
 * @since 1.0, 2020.06.05
 */
class AliexpressSolutionProductPostRequest
{
	/** 
	 * input param
	 **/
	private $postProductRequest;
	
	private $apiParas = array();
	
	public function setPostProductRequest($postProductRequest)
	{
		$this->postProductRequest = $postProductRequest;
		$this->apiParas["post_product_request"] = $postProductRequest;
	}

	public function getPostProductRequest()
	{
		return $this->postProductRequest;
	}

	public function getApiMethodName()
	{
		return "aliexpress.solution.product.post";
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
