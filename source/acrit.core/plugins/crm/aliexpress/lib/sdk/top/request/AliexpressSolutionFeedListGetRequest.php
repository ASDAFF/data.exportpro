<?php
/**
 * TOP API: aliexpress.solution.feed.list.get request
 * 
 * @author auto create
 * @since 1.0, 2019.11.26
 */
class AliexpressSolutionFeedListGetRequest
{
	/** 
	 * current page
	 **/
	private $currentPage;
	
	/** 
	 * feed type
	 **/
	private $feedType;
	
	/** 
	 * page size
	 **/
	private $pageSize;
	
	/** 
	 * status of the job, currently there are 3 types: FINISH, PROCESSING, QUEUEING
	 **/
	private $status;
	
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

	public function setFeedType($feedType)
	{
		$this->feedType = $feedType;
		$this->apiParas["feed_type"] = $feedType;
	}

	public function getFeedType()
	{
		return $this->feedType;
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

	public function setStatus($status)
	{
		$this->status = $status;
		$this->apiParas["status"] = $status;
	}

	public function getStatus()
	{
		return $this->status;
	}

	public function getApiMethodName()
	{
		return "aliexpress.solution.feed.list.get";
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
