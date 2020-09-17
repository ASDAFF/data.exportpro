<?php
/**
 * TOP API: aliexpress.message.redefining.versiontwo.querymsgdetaillist request
 * 
 * @author auto create
 * @since 1.0, 2018.07.25
 */
class AliexpressMessageRedefiningVersiontwoQuerymsgdetaillistRequest
{
	/** 
	 * 通道ID，即关系ID
	 **/
	private $channelId;
	
	/** 
	 * 当前页码
	 **/
	private $currentPage;
	
	/** 
	 * 商品ID或者订单ID,也可以为空
	 **/
	private $externId;
	
	/** 
	 * 每页条数,pageSize取值范围(0~100) 最多返回前5000条数据
	 **/
	private $pageSize;
	
	private $apiParas = array();
	
	public function setChannelId($channelId)
	{
		$this->channelId = $channelId;
		$this->apiParas["channel_id"] = $channelId;
	}

	public function getChannelId()
	{
		return $this->channelId;
	}

	public function setCurrentPage($currentPage)
	{
		$this->currentPage = $currentPage;
		$this->apiParas["current_page"] = $currentPage;
	}

	public function getCurrentPage()
	{
		return $this->currentPage;
	}

	public function setExternId($externId)
	{
		$this->externId = $externId;
		$this->apiParas["extern_id"] = $externId;
	}

	public function getExternId()
	{
		return $this->externId;
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

	public function getApiMethodName()
	{
		return "aliexpress.message.redefining.versiontwo.querymsgdetaillist";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->channelId,"channelId");
		RequestCheckUtil::checkNotNull($this->currentPage,"currentPage");
		RequestCheckUtil::checkNotNull($this->pageSize,"pageSize");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
