<?php

/**
 * 产品属性，以json格式进行封装后提交。参看aeopAeProductPropertys数据结构。此字段是否必填，需从类目接口getAttributesResultByCateId获取（即获取到的required来判断属性是否必填），该项只输入普通类目属性数据，不可输入sku类目属性。对于类目属性包含子类目属性的情况，此处不确认父属性和子属性，即选择任何属性，都以该对象提交。对于一个属性多个选中值的情况，以多个该对象存放。其中"attrNameId","attrValueId"为整型(Integer), "attrName", "attrValue"为字符串类型(String)。         i).  当设置一些系统属性时，如果类目自定义了一些候选值，只需要提供"attrNameId"和"attrValueId"即可。例如：{"attrNameId":494, "attrValueId":284}。         ii). 当设置一些需要手工输入属性内容时，只需要提供"attrNameId"和"attrValue"即可。例如：{"attrNameId": 1000, "attrValue": "test"}         iii）当设置自定义属性时，需要提供"attrName"和"attrValue"即可。例如: {"attrName": "Color", "attrValue": "red"}         iv） 当设置一个Other属性时，需要提供"attrNameId", "attrValueId", "attrValue"三个参数。例如：{"attrNameId": 1000, "attrValueId": 4, "attrValue": "Other Value"}。
 * @author auto create
 */
class AeopAeProductProperty
{
	
	/** 
	 * 自定义属性名属性名。 自定义属性名时,该项必填.
	 **/
	public $attr_name;
	
	/** 
	 * 属性名ID。从类目属性接口getAttributesResultByCateId获取普通类目属性，不可填入sku属性。 自定义属性名时,该项不填.
	 **/
	public $attr_name_id;
	
	/** 
	 * 自定义属性值。自定义属性名时,该项必填。 当自定义属性值内容为区间情况时，建议格式2 - 5 kg。(注意，数字'-'单位三者间是要加空格的！)
	 **/
	public $attr_value;
	
	/** 
	 * 自定义属性值的结束端
	 **/
	public $attr_value_end;
	
	/** 
	 * 属性值ID
	 **/
	public $attr_value_id;
	
	/** 
	 * 自定义属性值的开始端
	 **/
	public $attr_value_start;
	
	/** 
	 * 自定义属性值单位
	 **/
	public $attr_value_unit;	
}
?>