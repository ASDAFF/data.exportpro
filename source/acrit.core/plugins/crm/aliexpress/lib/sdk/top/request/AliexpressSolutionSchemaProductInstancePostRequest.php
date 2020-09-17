<?php
/**
 * TOP API: aliexpress.solution.schema.product.instance.post request
 * 
 * @author auto create
 * @since 1.0, 2019.05.10
 */
class AliexpressSolutionSchemaProductInstancePostRequest
{
	/** 
	 * Product instance data. Please note: the shipping_template_id should be replaced with your own shipping template id, which could be obtained through  https://developers.aliexpress.com/en/doc.htm?docId=43456&docType=2
	 **/
	private $productInstanceRequest;
	
	private $apiParas = array();
	
	public function setProductInstanceRequest($productInstanceRequest)
	{
		$this->productInstanceRequest = $productInstanceRequest;
		$this->apiParas["product_instance_request"] = $productInstanceRequest;
	}

	public function getProductInstanceRequest()
	{
		return $this->productInstanceRequest;
	}

	public function getApiMethodName()
	{
		return "aliexpress.solution.schema.product.instance.post";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->productInstanceRequest,"productInstanceRequest");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
