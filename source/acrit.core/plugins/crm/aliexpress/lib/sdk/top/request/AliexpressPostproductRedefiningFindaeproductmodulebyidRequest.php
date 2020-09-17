<?php
/**
 * TOP API: aliexpress.postproduct.redefining.findaeproductmodulebyid request
 * 
 * @author auto create
 * @since 1.0, 2018.07.25
 */
class AliexpressPostproductRedefiningFindaeproductmodulebyidRequest
{
	/** 
	 * moduleId 对应商品详情中的kse标签中的id属性;如: id="1004"
	 **/
	private $moduleId;
	
	private $apiParas = array();
	
	public function setModuleId($moduleId)
	{
		$this->moduleId = $moduleId;
		$this->apiParas["module_id"] = $moduleId;
	}

	public function getModuleId()
	{
		return $this->moduleId;
	}

	public function getApiMethodName()
	{
		return "aliexpress.postproduct.redefining.findaeproductmodulebyid";
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
