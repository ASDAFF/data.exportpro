<?php
/**
 * TOP API: aliexpress.solution.seller.category.tree.query request
 * 
 * @author auto create
 * @since 1.0, 2019.09.05
 */
class AliexpressSolutionSellerCategoryTreeQueryRequest
{
	/** 
	 * parent category ID. To obtain the root categories, setting the category_id = 0
	 **/
	private $categoryId;
	
	/** 
	 * filter the categories which seller does not have permission
	 **/
	private $filterNoPermission;
	
	private $apiParas = array();
	
	public function setCategoryId($categoryId)
	{
		$this->categoryId = $categoryId;
		$this->apiParas["category_id"] = $categoryId;
	}

	public function getCategoryId()
	{
		return $this->categoryId;
	}

	public function setFilterNoPermission($filterNoPermission)
	{
		$this->filterNoPermission = $filterNoPermission;
		$this->apiParas["filter_no_permission"] = $filterNoPermission;
	}

	public function getFilterNoPermission()
	{
		return $this->filterNoPermission;
	}

	public function getApiMethodName()
	{
		return "aliexpress.solution.seller.category.tree.query";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->categoryId,"categoryId");
		RequestCheckUtil::checkNotNull($this->filterNoPermission,"filterNoPermission");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
