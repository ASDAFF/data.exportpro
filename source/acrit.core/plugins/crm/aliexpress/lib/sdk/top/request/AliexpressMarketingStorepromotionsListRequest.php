<?php
/**
 * TOP API: aliexpress.marketing.storepromotions.list request
 * 
 * @author auto create
 * @since 1.0, 2019.07.08
 */
class AliexpressMarketingStorepromotionsListRequest
{
	/** 
	 * 查询参数
	 **/
	private $promotionQueryDto;
	
	private $apiParas = array();
	
	public function setPromotionQueryDto($promotionQueryDto)
	{
		$this->promotionQueryDto = $promotionQueryDto;
		$this->apiParas["promotion_query_dto"] = $promotionQueryDto;
	}

	public function getPromotionQueryDto()
	{
		return $this->promotionQueryDto;
	}

	public function getApiMethodName()
	{
		return "aliexpress.marketing.storepromotions.list";
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
