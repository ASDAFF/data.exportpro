<?php
/**
 * TOP API: aliexpress.trade.new.redefining.findorderbyid request
 * 
 * @author auto create
 * @since 1.0, 2020.03.12
 */
class AliexpressTradeNewRedefiningFindorderbyidRequest
{
	/** 
	 * 详细参考如下
	 **/
	private $param1;
	
	private $apiParas = array();
	
	public function setParam1($param1)
	{
		$this->param1 = $param1;
		$this->apiParas["param1"] = $param1;
	}

	public function getParam1()
	{
		return $this->param1;
	}

	public function getApiMethodName()
	{
		return "aliexpress.trade.new.redefining.findorderbyid";
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
