<?php
/**
 * TOP API: cainiao.global.handover.cancel request
 * 
 * @author auto create
 * @since 1.0, 2020.02.10
 */
class CainiaoGlobalHandoverCancelRequest
{
	/** 
	 * ISV名称，ISV：ISV-ISV英文或拼音名称、商家ERP：SELLER-商家英文或拼音名称
	 **/
	private $client;
	
	/** 
	 * 要取消的交接物id，即大包id
	 **/
	private $handoverContentId;
	
	/** 
	 * 要取消的交接单id
	 **/
	private $handoverOrderId;
	
	/** 
	 * 多语言
	 **/
	private $locale;
	
	/** 
	 * 要取消的交接物运单号，即大包运单号
	 **/
	private $trackingNumber;
	
	/** 
	 * 系统自动生成
	 **/
	private $userInfo;
	
	private $apiParas = array();
	
	public function setClient($client)
	{
		$this->client = $client;
		$this->apiParas["client"] = $client;
	}

	public function getClient()
	{
		return $this->client;
	}

	public function setHandoverContentId($handoverContentId)
	{
		$this->handoverContentId = $handoverContentId;
		$this->apiParas["handover_content_id"] = $handoverContentId;
	}

	public function getHandoverContentId()
	{
		return $this->handoverContentId;
	}

	public function setHandoverOrderId($handoverOrderId)
	{
		$this->handoverOrderId = $handoverOrderId;
		$this->apiParas["handover_order_id"] = $handoverOrderId;
	}

	public function getHandoverOrderId()
	{
		return $this->handoverOrderId;
	}

	public function setLocale($locale)
	{
		$this->locale = $locale;
		$this->apiParas["locale"] = $locale;
	}

	public function getLocale()
	{
		return $this->locale;
	}

	public function setTrackingNumber($trackingNumber)
	{
		$this->trackingNumber = $trackingNumber;
		$this->apiParas["tracking_number"] = $trackingNumber;
	}

	public function getTrackingNumber()
	{
		return $this->trackingNumber;
	}

	public function setUserInfo($userInfo)
	{
		$this->userInfo = $userInfo;
		$this->apiParas["user_info"] = $userInfo;
	}

	public function getUserInfo()
	{
		return $this->userInfo;
	}

	public function getApiMethodName()
	{
		return "cainiao.global.handover.cancel";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->client,"client");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
