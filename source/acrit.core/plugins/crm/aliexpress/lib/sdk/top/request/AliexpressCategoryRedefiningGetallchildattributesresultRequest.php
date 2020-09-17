<?php
/**
 * TOP API: aliexpress.category.redefining.getallchildattributesresult request
 * 
 * @author auto create
 * @since 1.0, 2019.12.26
 */
class AliexpressCategoryRedefiningGetallchildattributesresultRequest
{
	/** 
	 * 叶子类目ID。通过产品获取类目ID，如果只传cid，则返回一级属性。
	 **/
	private $cateId;
	
	/** 
	 * 获取属性值文本对应的多语言信息
	 **/
	private $locale;
	
	/** 
	 * 类目子属性路径,由该子属性上层的类目属性id和类目属性值id组成,格式参考示例，多个用逗号隔开，第二个属性及为第一个的属性值的子属性，第三个属性为第二个属性的子属性，以此类推。如需返回此类目对应的子属性，则需同cateid一起进行提交。
	 **/
	private $parentAttrvalueList;
	
	private $apiParas = array();
	
	public function setCateId($cateId)
	{
		$this->cateId = $cateId;
		$this->apiParas["cate_id"] = $cateId;
	}

	public function getCateId()
	{
		return $this->cateId;
	}

	public function setLocale($locale)
	{
		$this->locale = $locale;
		$this->apiParas["locale"] = $locale;
	}

	public function getLocale()
	{
		return $this->locale;
	}

	public function setParentAttrvalueList($parentAttrvalueList)
	{
		$this->parentAttrvalueList = $parentAttrvalueList;
		$this->apiParas["parent_attrvalue_list"] = $parentAttrvalueList;
	}

	public function getParentAttrvalueList()
	{
		return $this->parentAttrvalueList;
	}

	public function getApiMethodName()
	{
		return "aliexpress.category.redefining.getallchildattributesresult";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->cateId,"cateId");
		RequestCheckUtil::checkMaxListSize($this->parentAttrvalueList,20,"parentAttrvalueList");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
