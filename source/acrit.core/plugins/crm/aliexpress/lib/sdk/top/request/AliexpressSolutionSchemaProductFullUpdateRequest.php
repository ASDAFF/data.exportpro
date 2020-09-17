<?php
/**
 * TOP API: aliexpress.solution.schema.product.full.update request
 * 
 * @author auto create
 * @since 1.0, 2019.07.10
 */
class AliexpressSolutionSchemaProductFullUpdateRequest
{
	/** 
	 * Product full update request. To learn how to generate the content, please refer to https://developers.aliexpress.com/en/doc.htm?docId=109760&docType=1.  Be aware that the aliexpress_product_id field should be replaced by the product ID belonged to the seller.
	 **/
	private $schemaFullUpdateRequest;
	
	private $apiParas = array();
	
	public function setSchemaFullUpdateRequest($schemaFullUpdateRequest)
	{
		$this->schemaFullUpdateRequest = $schemaFullUpdateRequest;
		$this->apiParas["schema_full_update_request"] = $schemaFullUpdateRequest;
	}

	public function getSchemaFullUpdateRequest()
	{
		return $this->schemaFullUpdateRequest;
	}

	public function getApiMethodName()
	{
		return "aliexpress.solution.schema.product.full.update";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->schemaFullUpdateRequest,"schemaFullUpdateRequest");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
