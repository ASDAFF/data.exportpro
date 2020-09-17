<?php
/**
 * TOP API: aliexpress.data.redefining.queryproductviewedinfoeverydaybyid request
 * 
 * @author auto create
 * @since 1.0, 2018.07.25
 */
class AliexpressDataRedefiningQueryproductviewedinfoeverydaybyidRequest
{
	/** 
	 * 当前页码
	 **/
	private $currentPage;
	
	/** 
	 * 查询时间段的截止时间。例如：yyyy-mm-dd
	 **/
	private $endDate;
	
	/** 
	 * 每页结果数量，默认20个，最大值 50
	 **/
	private $pageSize;
	
	/** 
	 * 商品id
	 **/
	private $productId;
	
	/** 
	 * 查询时间段的开始时间。例如：yyyy-mm-dd
	 **/
	private $startDate;
	
	private $apiParas = array();
	
	public function setCurrentPage($currentPage)
	{
		$this->currentPage = $currentPage;
		$this->apiParas["current_page"] = $currentPage;
	}

	public function getCurrentPage()
	{
		return $this->currentPage;
	}

	public function setEndDate($endDate)
	{
		$this->endDate = $endDate;
		$this->apiParas["end_date"] = $endDate;
	}

	public function getEndDate()
	{
		return $this->endDate;
	}

	public function setPageSize($pageSize)
	{
		$this->pageSize = $pageSize;
		$this->apiParas["page_size"] = $pageSize;
	}

	public function getPageSize()
	{
		return $this->pageSize;
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

	public function setStartDate($startDate)
	{
		$this->startDate = $startDate;
		$this->apiParas["start_date"] = $startDate;
	}

	public function getStartDate()
	{
		return $this->startDate;
	}

	public function getApiMethodName()
	{
		return "aliexpress.data.redefining.queryproductviewedinfoeverydaybyid";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->currentPage,"currentPage");
		RequestCheckUtil::checkNotNull($this->endDate,"endDate");
		RequestCheckUtil::checkNotNull($this->productId,"productId");
		RequestCheckUtil::checkNotNull($this->startDate,"startDate");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
