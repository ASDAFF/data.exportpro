<?php
/**
 * TOP API: aliexpress.category.redefining.getchildattributesresultbypostcateidandpath request
 * 
 * @author auto create
 * @since 1.0, 2019.12.30
 */
class AliexpressCategoryRedefiningGetchildattributesresultbypostcateidandpathRequest
{
	/** 
	 * 指定获取语种的属性值文本
	 **/
	private $locale;
	
	/** 
	 * 叶子类目ID。通过产品获取类目ID，如果只传cid，则返回一级属性。
	 **/
	private $param1;
	
	/** 
	 * 类目子属性路径,由该子属性上层的类目属性id和类目属性值id组成,格式参考示例，多个用逗号隔开，第二个属性及为第一个的属性值的子属性，第三个属性为第二个属性的子属性，以此类推。如需返回此类目对应的子属性，则需同cateid一起进行提交。
	 **/
	private $param2;
	
	private $apiParas = array();
	
	public function setLocale($locale)
	{
		$this->locale = $locale;
		$this->apiParas["locale"] = $locale;
	}

	public function getLocale()
	{
		return $this->locale;
	}

	public function setParam1($param1)
	{
		$this->param1 = $param1;
		$this->apiParas["param1"] = $param1;
	}

	public function getParam1()
	{
		return $this->param1;
	}

	public function setParam2($param2)
	{
		$this->param2 = $param2;
		$this->apiParas["param2"] = $param2;
	}

	public function getParam2()
	{
		return $this->param2;
	}

	public function getApiMethodName()
	{
		return "aliexpress.category.redefining.getchildattributesresultbypostcateidandpath";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkMaxListSize($this->param2,20,"param2");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
