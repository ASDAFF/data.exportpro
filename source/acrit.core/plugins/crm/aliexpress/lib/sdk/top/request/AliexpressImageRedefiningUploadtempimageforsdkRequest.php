<?php
/**
 * TOP API: aliexpress.image.redefining.uploadtempimageforsdk request
 * 
 * @author auto create
 * @since 1.0, 2018.07.25
 */
class AliexpressImageRedefiningUploadtempimageforsdkRequest
{
	/** 
	 * 字符串形式的图片文件二进制数据流
	 **/
	private $fileData;
	
	/** 
	 * 图片原名。支持的图片格式：jpg, jpeg, gif
	 **/
	private $srcFileName;
	
	private $apiParas = array();
	
	public function setFileData($fileData)
	{
		$this->fileData = $fileData;
		$this->apiParas["file_data"] = $fileData;
	}

	public function getFileData()
	{
		return $this->fileData;
	}

	public function setSrcFileName($srcFileName)
	{
		$this->srcFileName = $srcFileName;
		$this->apiParas["src_file_name"] = $srcFileName;
	}

	public function getSrcFileName()
	{
		return $this->srcFileName;
	}

	public function getApiMethodName()
	{
		return "aliexpress.image.redefining.uploadtempimageforsdk";
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
