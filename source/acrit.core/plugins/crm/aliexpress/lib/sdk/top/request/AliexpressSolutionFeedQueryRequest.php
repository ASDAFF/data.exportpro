<?php
/**
 * TOP API: aliexpress.solution.feed.query request
 * 
 * @author auto create
 * @since 1.0, 2019.06.05
 */
class AliexpressSolutionFeedQueryRequest
{
	/** 
	 * job id
	 **/
	private $jobId;
	
	private $apiParas = array();
	
	public function setJobId($jobId)
	{
		$this->jobId = $jobId;
		$this->apiParas["job_id"] = $jobId;
	}

	public function getJobId()
	{
		return $this->jobId;
	}

	public function getApiMethodName()
	{
		return "aliexpress.solution.feed.query";
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
