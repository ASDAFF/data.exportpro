<?php
/**
 * TOP API: aliexpress.offer.draftproducts.get request
 * 
 * @author auto create
 * @since 1.0, 2019.01.10
 */
class AliexpressOfferDraftproductsGetRequest
{
	/** 
	 * none
	 **/
	private $aeopAEProductListQuery;
	
	private $apiParas = array();
	
	public function setAeopAEProductListQuery($aeopAEProductListQuery)
	{
		$this->aeopAEProductListQuery = $aeopAEProductListQuery;
		$this->apiParas["aeop_a_e_product_list_query"] = $aeopAEProductListQuery;
	}

	public function getAeopAEProductListQuery()
	{
		return $this->aeopAEProductListQuery;
	}

	public function getApiMethodName()
	{
		return "aliexpress.offer.draftproducts.get";
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
