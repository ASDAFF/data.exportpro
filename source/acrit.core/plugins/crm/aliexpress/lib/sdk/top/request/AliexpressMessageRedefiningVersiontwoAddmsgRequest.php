<?php
/**
 * TOP API: aliexpress.message.redefining.versiontwo.addmsg request
 * 
 * @author auto create
 * @since 1.0, 2018.07.25
 */
class AliexpressMessageRedefiningVersiontwoAddmsgRequest
{
	/** 
	 * 消息发送对象
	 **/
	private $createParam;
	
	private $apiParas = array();
	
	public function setCreateParam($createParam)
	{
		$this->createParam = $createParam;
		$this->apiParas["create_param"] = $createParam;
	}

	public function getCreateParam()
	{
		return $this->createParam;
	}

	public function getApiMethodName()
	{
		return "aliexpress.message.redefining.versiontwo.addmsg";
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
