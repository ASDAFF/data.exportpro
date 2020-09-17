<?php
/**
 * TOP API: aliexpress.merchant.redefining.queryremarks request
 * 
 * @author auto create
 * @since 1.0, 2018.07.25
 */
class AliexpressMerchantRedefiningQueryremarksRequest
{
	/** 
	 * 业务类型，0 为订单备注。
	 **/
	private $bizType;
	
	/** 
	 * 业务类型为订单备注，则remark_ids为订单ID列表。
	 **/
	private $remarkIds;
	
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

	public function setRemarkIds($remarkIds)
	{
		$this->remarkIds = $remarkIds;
		$this->apiParas["remark_ids"] = $remarkIds;
	}

	public function getRemarkIds()
	{
		return $this->remarkIds;
	}

	public function getApiMethodName()
	{
		return "aliexpress.merchant.redefining.queryremarks";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkMaxListSize($this->remarkIds,100,"remarkIds");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
