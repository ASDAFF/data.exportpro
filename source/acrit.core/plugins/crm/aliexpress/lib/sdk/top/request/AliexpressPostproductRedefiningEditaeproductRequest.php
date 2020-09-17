<?php
/**
 * TOP API: aliexpress.postproduct.redefining.editaeproduct request
 * 
 * @author auto create
 * @since 1.0, 2020.05.09
 */
class AliexpressPostproductRedefiningEditaeproductRequest
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
		return "aliexpress.postproduct.redefining.editaeproduct";
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
