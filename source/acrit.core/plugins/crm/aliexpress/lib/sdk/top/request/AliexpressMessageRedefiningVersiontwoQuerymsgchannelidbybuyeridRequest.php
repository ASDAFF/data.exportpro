<?php
/**
 * TOP API: aliexpress.message.redefining.versiontwo.querymsgchannelidbybuyerid request
 * 
 * @author auto create
 * @since 1.0, 2018.07.25
 */
class AliexpressMessageRedefiningVersiontwoQuerymsgchannelidbybuyeridRequest
{
	/** 
	 * 用户登陆账号
	 **/
	private $buyerId;
	
	private $apiParas = array();
	
	public function setBuyerId($buyerId)
	{
		$this->buyerId = $buyerId;
		$this->apiParas["buyer_id"] = $buyerId;
	}

	public function getBuyerId()
	{
		return $this->buyerId;
	}

	public function getApiMethodName()
	{
		return "aliexpress.message.redefining.versiontwo.querymsgchannelidbybuyerid";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->buyerId,"buyerId");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
