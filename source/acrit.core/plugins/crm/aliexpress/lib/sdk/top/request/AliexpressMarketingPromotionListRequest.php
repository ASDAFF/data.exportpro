<?php
/**
 * TOP API: aliexpress.marketing.promotion.list request
 * 
 * @author auto create
 * @since 1.0, 2018.07.25
 */
class AliexpressMarketingPromotionListRequest
{
	/** 
	 * 查询参数
	 **/
	private $marketingPromotionQuery;
	
	private $apiParas = array();
	
	public function setMarketingPromotionQuery($marketingPromotionQuery)
	{
		$this->marketingPromotionQuery = $marketingPromotionQuery;
		$this->apiParas["marketing_promotion_query"] = $marketingPromotionQuery;
	}

	public function getMarketingPromotionQuery()
	{
		return $this->marketingPromotionQuery;
	}

	public function getApiMethodName()
	{
		return "aliexpress.marketing.promotion.list";
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
