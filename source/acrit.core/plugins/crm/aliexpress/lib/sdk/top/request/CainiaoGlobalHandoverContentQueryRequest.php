<?php
/**
 * TOP API: cainiao.global.handover.content.query request
 * 
 * @author auto create
 * @since 1.0, 2020.02.10
 */
class CainiaoGlobalHandoverContentQueryRequest
{
	/** 
	 * 客户端名称，ISV：ISV-ISV英文或拼音名称、商家ERP：SELLER-商家英文或拼音名称
	 **/
	private $client;
	
	/** 
	 * 多语言
	 **/
	private $locale;
	
	/** 
	 * 交接物物流订单编码,和交接物运单号参数可以任选其一即可
	 **/
	private $orderCode;
	
	/** 
	 * 交接物运单号，和交接物物流订单编码参数任选其一即可
	 **/
	private $trackingNumber;
	
	/** 
	 * 用户信息
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

	public function setLocale($locale)
	{
		$this->locale = $locale;
		$this->apiParas["locale"] = $locale;
	}

	public function getLocale()
	{
		return $this->locale;
	}

	public function setOrderCode($orderCode)
	{
		$this->orderCode = $orderCode;
		$this->apiParas["order_code"] = $orderCode;
	}

	public function getOrderCode()
	{
		return $this->orderCode;
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
		return "cainiao.global.handover.content.query";
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
