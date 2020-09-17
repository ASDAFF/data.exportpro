<?php
/**
 * TOP API: aliexpress.postproduct.redefining.findproductinfolistquery request
 * 
 * @author auto create
 * @since 1.0, 2019.06.20
 */
class AliexpressPostproductRedefiningFindproductinfolistqueryRequest
{
	/** 
	 * 商品列表查询
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
		return "aliexpress.postproduct.redefining.findproductinfolistquery";
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
