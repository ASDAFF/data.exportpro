<?php
/**
 * TOP API: aliexpress.issue.image.upload request
 * 
 * @author auto create
 * @since 1.0, 2018.07.25
 */
class AliexpressIssueImageUploadRequest
{
	/** 
	 * 买家登录帐号
	 **/
	private $buyerLoginId;
	
	/** 
	 * 文件后缀名
	 **/
	private $extension;
	
	/** 
	 * 图片内容
	 **/
	private $imageBytes;
	
	/** 
	 * 纠纷id
	 **/
	private $issueId;
	
	private $apiParas = array();
	
	public function setBuyerLoginId($buyerLoginId)
	{
		$this->buyerLoginId = $buyerLoginId;
		$this->apiParas["buyer_login_id"] = $buyerLoginId;
	}

	public function getBuyerLoginId()
	{
		return $this->buyerLoginId;
	}

	public function setExtension($extension)
	{
		$this->extension = $extension;
		$this->apiParas["extension"] = $extension;
	}

	public function getExtension()
	{
		return $this->extension;
	}

	public function setImageBytes($imageBytes)
	{
		$this->imageBytes = $imageBytes;
		$this->apiParas["image_bytes"] = $imageBytes;
	}

	public function getImageBytes()
	{
		return $this->imageBytes;
	}

	public function setIssueId($issueId)
	{
		$this->issueId = $issueId;
		$this->apiParas["issue_id"] = $issueId;
	}

	public function getIssueId()
	{
		return $this->issueId;
	}

	public function getApiMethodName()
	{
		return "aliexpress.issue.image.upload";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->buyerLoginId,"buyerLoginId");
		RequestCheckUtil::checkNotNull($this->extension,"extension");
		RequestCheckUtil::checkNotNull($this->issueId,"issueId");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
