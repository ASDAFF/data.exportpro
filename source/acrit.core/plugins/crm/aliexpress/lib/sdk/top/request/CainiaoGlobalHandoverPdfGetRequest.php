<?php
/**
 * TOP API: cainiao.global.handover.pdf.get request
 * 
 * @author auto create
 * @since 1.0, 2020.02.11
 */
class CainiaoGlobalHandoverPdfGetRequest
{
	/** 
	 * 客户端名称，ISV：ISV-ISV英文或拼音名称、商家ERP：SELLER-商家英文或拼音名称
	 **/
	private $client;
	
	/** 
	 * 大包编号id
	 **/
	private $handoverContentId;
	
	/** 
	 * 多语言
	 **/
	private $locale;
	
	/** 
	 * 打印数据类型，1：面单、4：发货标签
	 **/
	private $type;
	
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

	public function setHandoverContentId($handoverContentId)
	{
		$this->handoverContentId = $handoverContentId;
		$this->apiParas["handover_content_id"] = $handoverContentId;
	}

	public function getHandoverContentId()
	{
		return $this->handoverContentId;
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

	public function setType($type)
	{
		$this->type = $type;
		$this->apiParas["type"] = $type;
	}

	public function getType()
	{
		return $this->type;
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
		return "cainiao.global.handover.pdf.get";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->client,"client");
		RequestCheckUtil::checkNotNull($this->handoverContentId,"handoverContentId");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
