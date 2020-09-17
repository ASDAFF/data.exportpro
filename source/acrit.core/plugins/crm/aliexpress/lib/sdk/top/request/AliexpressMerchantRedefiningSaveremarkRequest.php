<?php
/**
 * TOP API: aliexpress.merchant.redefining.saveremark request
 * 
 * @author auto create
 * @since 1.0, 2018.07.25
 */
class AliexpressMerchantRedefiningSaveremarkRequest
{
	/** 
	 * 业务类型，默认为订单备注
	 **/
	private $bizType;
	
	/** 
	 * 备注内容
	 **/
	private $content;
	
	/** 
	 * 备注Id
	 **/
	private $remarkId;
	
	private $apiParas = array();
	
	public function setBizType($bizType)
	{
		$this->bizType = $bizType;
		$this->apiParas["biz_type"] = $bizType;
	}

	public function getBizType()
	{
		return $this->bizType;
	}

	public function setContent($content)
	{
		$this->content = $content;
		$this->apiParas["content"] = $content;
	}

	public function getContent()
	{
		return $this->content;
	}

	public function setRemarkId($remarkId)
	{
		$this->remarkId = $remarkId;
		$this->apiParas["remark_id"] = $remarkId;
	}

	public function getRemarkId()
	{
		return $this->remarkId;
	}

	public function getApiMethodName()
	{
		return "aliexpress.merchant.redefining.saveremark";
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
