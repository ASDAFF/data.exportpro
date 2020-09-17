<?php
/**
 * TOP API: aliexpress.postproduct.redefining.findaeproductdetailmodulelistbyqurey request
 * 
 * @author auto create
 * @since 1.0, 2018.07.25
 */
class AliexpressPostproductRedefiningFindaeproductdetailmodulelistbyqureyRequest
{
	/** 
	 * 要查询模块的状态，包含：tbd(审核不通过),auditing（审核中）,approved（审核通过）
	 **/
	private $moduleStatus;
	
	/** 
	 * 要查询当前页码，每页返回50条记录，从1开始
	 **/
	private $pageIndex;
	
	/** 
	 * 要查询模块的类型，包含：custom（自定义模块）,relation（关联模块）
	 **/
	private $type;
	
	private $apiParas = array();
	
	public function setModuleStatus($moduleStatus)
	{
		$this->moduleStatus = $moduleStatus;
		$this->apiParas["module_status"] = $moduleStatus;
	}

	public function getModuleStatus()
	{
		return $this->moduleStatus;
	}

	public function setPageIndex($pageIndex)
	{
		$this->pageIndex = $pageIndex;
		$this->apiParas["page_index"] = $pageIndex;
	}

	public function getPageIndex()
	{
		return $this->pageIndex;
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

	public function getApiMethodName()
	{
		return "aliexpress.postproduct.redefining.findaeproductdetailmodulelistbyqurey";
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
