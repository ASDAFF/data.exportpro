<?php
/**
 * TOP API: aliexpress.postproduct.redefining.editproductcidattidsku request
 * 
 * @author auto create
 * @since 1.0, 2020.05.20
 */
class AliexpressPostproductRedefiningEditproductcidattidskuRequest
{
	/** 
	 * 产品类目ID，如果不想调整类目，则不要填写。
	 **/
	private $categoryId;
	
	/** 
	 * 必填，商品id，一次只能上传一个
	 **/
	private $productId;
	
	/** 
	 * 该产品新的类目属性。如果没有指定categoryId, 则该类目属性则为当前产品所属类目下的类目属性，如果指定了categoryId, 则该类目属性则为新类目下的类目属性。
	 **/
	private $productProperties;
	
	/** 
	 * 该产品新的类目SKU属性。如果没有指定categoryId, 则该SKU属性则为当前产品所属类目下的SKU属性，如果指定了categoryId, 则该SKU属性则为新类目下的SKU属性。特别提示：新增SKU实际可售库存属性ipmSkuStock，该属性值的合理取值范围为0~999999，如该商品有SKU时，请确保至少有一个SKU是有货状态，也就是ipmSkuStock取值是1~999999，在整个商品纬度库存值的取值范围是1~999999。
	 **/
	private $productSkus;
	
	private $apiParas = array();
	
	public function setCategoryId($categoryId)
	{
		$this->categoryId = $categoryId;
		$this->apiParas["category_id"] = $categoryId;
	}

	public function getCategoryId()
	{
		return $this->categoryId;
	}

	public function setProductId($productId)
	{
		$this->productId = $productId;
		$this->apiParas["product_id"] = $productId;
	}

	public function getProductId()
	{
		return $this->productId;
	}

	public function setProductProperties($productProperties)
	{
		$this->productProperties = $productProperties;
		$this->apiParas["product_properties"] = $productProperties;
	}

	public function getProductProperties()
	{
		return $this->productProperties;
	}

	public function setProductSkus($productSkus)
	{
		$this->productSkus = $productSkus;
		$this->apiParas["product_skus"] = $productSkus;
	}

	public function getProductSkus()
	{
		return $this->productSkus;
	}

	public function getApiMethodName()
	{
		return "aliexpress.postproduct.redefining.editproductcidattidsku";
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
