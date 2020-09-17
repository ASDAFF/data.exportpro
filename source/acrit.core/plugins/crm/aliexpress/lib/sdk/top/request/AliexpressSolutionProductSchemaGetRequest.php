<?php
/**
 * TOP API: aliexpress.solution.product.schema.get request
 * 
 * @author auto create
 * @since 1.0, 2019.05.06
 */
class AliexpressSolutionProductSchemaGetRequest
{
	/** 
	 * aliexpress category id. You can get it from category API
	 **/
	private $aliexpressCategoryId;
	
	private $apiParas = array();
	
	public function setAliexpressCategoryId($aliexpressCategoryId)
	{
		$this->aliexpressCategoryId = $aliexpressCategoryId;
		$this->apiParas["aliexpress_category_id"] = $aliexpressCategoryId;
	}

	public function getAliexpressCategoryId()
	{
		return $this->aliexpressCategoryId;
	}

	public function getApiMethodName()
	{
		return "aliexpress.solution.product.schema.get";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->aliexpressCategoryId,"aliexpressCategoryId");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
