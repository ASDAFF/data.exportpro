<?php
/**
 * TOP API: aliexpress.photobank.redefining.queryphotobankimagebypaths request
 * 
 * @author auto create
 * @since 1.0, 2018.07.25
 */
class AliexpressPhotobankRedefiningQueryphotobankimagebypathsRequest
{
	/** 
	 * 图片文件名，多个文件用逗号分隔
	 **/
	private $paths;
	
	private $apiParas = array();
	
	public function setPaths($paths)
	{
		$this->paths = $paths;
		$this->apiParas["paths"] = $paths;
	}

	public function getPaths()
	{
		return $this->paths;
	}

	public function getApiMethodName()
	{
		return "aliexpress.photobank.redefining.queryphotobankimagebypaths";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkMaxListSize($this->paths,20,"paths");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
