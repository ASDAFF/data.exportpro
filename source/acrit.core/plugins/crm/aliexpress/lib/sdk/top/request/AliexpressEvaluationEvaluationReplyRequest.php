<?php
/**
 * TOP API: aliexpress.evaluation.evaluation.reply request
 * 
 * @author auto create
 * @since 1.0, 2018.07.25
 */
class AliexpressEvaluationEvaluationReplyRequest
{
	/** 
	 * 要回复的子订单id
	 **/
	private $childOrderId;
	
	/** 
	 * 父订单id
	 **/
	private $parentOrderId;
	
	/** 
	 * 回复内容
	 **/
	private $text;
	
	private $apiParas = array();
	
	public function setChildOrderId($childOrderId)
	{
		$this->childOrderId = $childOrderId;
		$this->apiParas["child_order_id"] = $childOrderId;
	}

	public function getChildOrderId()
	{
		return $this->childOrderId;
	}

	public function setParentOrderId($parentOrderId)
	{
		$this->parentOrderId = $parentOrderId;
		$this->apiParas["parent_order_id"] = $parentOrderId;
	}

	public function getParentOrderId()
	{
		return $this->parentOrderId;
	}

	public function setText($text)
	{
		$this->text = $text;
		$this->apiParas["text"] = $text;
	}

	public function getText()
	{
		return $this->text;
	}

	public function getApiMethodName()
	{
		return "aliexpress.evaluation.evaluation.reply";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->childOrderId,"childOrderId");
		RequestCheckUtil::checkNotNull($this->parentOrderId,"parentOrderId");
		RequestCheckUtil::checkNotNull($this->text,"text");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
