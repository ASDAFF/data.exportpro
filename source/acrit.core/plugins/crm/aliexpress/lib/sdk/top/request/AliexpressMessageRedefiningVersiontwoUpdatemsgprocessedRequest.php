<?php
/**
 * TOP API: aliexpress.message.redefining.versiontwo.updatemsgprocessed request
 * 
 * @author auto create
 * @since 1.0, 2018.07.25
 */
class AliexpressMessageRedefiningVersiontwoUpdatemsgprocessedRequest
{
	/** 
	 * 通道ID(即关系ID)
	 **/
	private $channelId;
	
	/** 
	 * 处理状态(0未处理,1已处理)
	 **/
	private $dealStat;
	
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

	public function setDealStat($dealStat)
	{
		$this->dealStat = $dealStat;
		$this->apiParas["deal_stat"] = $dealStat;
	}

	public function getDealStat()
	{
		return $this->dealStat;
	}

	public function getApiMethodName()
	{
		return "aliexpress.message.redefining.versiontwo.updatemsgprocessed";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->channelId,"channelId");
		RequestCheckUtil::checkNotNull($this->dealStat,"dealStat");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
