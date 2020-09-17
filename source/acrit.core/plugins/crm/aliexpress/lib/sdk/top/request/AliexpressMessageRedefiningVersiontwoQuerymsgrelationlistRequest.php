<?php
/**
 * TOP API: aliexpress.message.redefining.versiontwo.querymsgrelationlist request
 * 
 * @author auto create
 * @since 1.0, 2018.07.25
 */
class AliexpressMessageRedefiningVersiontwoQuerymsgrelationlistRequest
{
	/** 
	 * 查询入参对象
	 **/
	private $query;
	
	private $apiParas = array();
	
	public function setQuery($query)
	{
		$this->query = $query;
		$this->apiParas["query"] = $query;
	}

	public function getQuery()
	{
		return $this->query;
	}

	public function getApiMethodName()
	{
		return "aliexpress.message.redefining.versiontwo.querymsgrelationlist";
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
