<?php
/**
 * TOP API: aliexpress.offer.product.skuprices.edit request
 * 
 * @author auto create
 * @since 1.0, 2019.08.15
 */
class AliexpressOfferProductSkupricesEditRequest
{
	/** 
	 * 商品id
	 **/
	private $productId;
	
	/** 
	 * 商品sku与价格映射表。 SKU的价格信息(一个或着多个),格式{"skuid1":price1,"skuid2":price2}； 其中skuid可以通过api.findAeProductById接口中的aeopAeproductSKUs列表中各个sku对象中id字段值进行获取, 没有sku属性的商品其id回传“”值
	 **/
	private $skuIdPriceMap;
	
	private $apiParas = array();
	
	public function setProductId($productId)
	{
		$this->productId = $productId;
		$this->apiParas["product_id"] = $productId;
	}

	public function getProductId()
	{
		return $this->productId;
	}

	public function setSkuIdPriceMap($skuIdPriceMap)
	{
		$this->skuIdPriceMap = $skuIdPriceMap;
		$this->apiParas["sku_id_price_map"] = $skuIdPriceMap;
	}

	public function getSkuIdPriceMap()
	{
		return $this->skuIdPriceMap;
	}

	public function getApiMethodName()
	{
		return "aliexpress.offer.product.skuprices.edit";
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
