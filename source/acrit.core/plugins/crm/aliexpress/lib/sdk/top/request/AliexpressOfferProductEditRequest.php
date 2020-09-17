<?php
/**
 * TOP API: aliexpress.offer.product.edit request
 * 
 * @author auto create
 * @since 1.0, 2020.01.20
 */
class AliexpressOfferProductEditRequest
{
	/** 
	 * 产品信息
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
		return "aliexpress.offer.product.edit";
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
