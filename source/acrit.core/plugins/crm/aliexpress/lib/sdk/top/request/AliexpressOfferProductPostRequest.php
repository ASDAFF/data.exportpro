<?php
/**
 * TOP API: aliexpress.offer.product.post request
 * 
 * @author auto create
 * @since 1.0, 2020.01.03
 */
class AliexpressOfferProductPostRequest
{
	/** 
	 * 待发布商品数据
	 **/
	private $aeopAEProduct;
	
	private $apiParas = array();
	
	public function setAeopAEProduct($aeopAEProduct)
	{
		$this->aeopAEProduct = $aeopAEProduct;
		$this->apiParas["aeop_a_e_product"] = $aeopAEProduct;
	}

	public function getAeopAEProduct()
	{
		return $this->aeopAEProduct;
	}

	public function getApiMethodName()
	{
		return "aliexpress.offer.product.post";
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
