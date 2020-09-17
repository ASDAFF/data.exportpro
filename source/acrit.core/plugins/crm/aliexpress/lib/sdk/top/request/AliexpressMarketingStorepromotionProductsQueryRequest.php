<?php
/**
 * TOP API: aliexpress.marketing.storepromotion.products.query request
 * 
 * @author auto create
 * @since 1.0, 2019.07.08
 */
class AliexpressMarketingStorepromotionProductsQueryRequest
{
	/** 
	 * 入参
	 **/
	private $promotionProductQueryDto;
	
	private $apiParas = array();
	
	public function setPromotionProductQueryDto($promotionProductQueryDto)
	{
		$this->promotionProductQueryDto = $promotionProductQueryDto;
		$this->apiParas["promotion_product_query_dto"] = $promotionProductQueryDto;
	}

	public function getPromotionProductQueryDto()
	{
		return $this->promotionProductQueryDto;
	}

	public function getApiMethodName()
	{
		return "aliexpress.marketing.storepromotion.products.query";
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
