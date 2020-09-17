<?php
/**
 * TOP API: aliexpress.solution.order.fulfill request
 * 
 * @author auto create
 * @since 1.0, 2019.10.08
 */
class AliexpressSolutionOrderFulfillRequest
{
	/** 
	 * Remarks (only in English, and the length is limited to 512 characters)
	 **/
	private $description;
	
	/** 
	 * logistics number
	 **/
	private $logisticsNo;
	
	/** 
	 * order ID for delivery by the user
	 **/
	private $outRef;
	
	/** 
	 * Status including: all shipments (all), part of the delivery (part)
	 **/
	private $sendType;
	
	/** 
	 * Actual logistics service selected by the user (logistics service key: This interface obtains the currently supportable logistics according to all the supportable logistics services listed by api.listLogisticsService. Please visit the forum link http://bbs.seller.aliexpress.com/bbs/read.php?tid=266120&page=1&toread=1#tpc for the detailed list of logistics services supported by the platform.)
	 **/
	private $serviceName;
	
	/** 
	 * When serviceName=other, fill in the corresponding tracking website.
	 **/
	private $trackingWebsite;
	
	private $apiParas = array();
	
	public function setDescription($description)
	{
		$this->description = $description;
		$this->apiParas["description"] = $description;
	}

	public function getDescription()
	{
		return $this->description;
	}

	public function setLogisticsNo($logisticsNo)
	{
		$this->logisticsNo = $logisticsNo;
		$this->apiParas["logistics_no"] = $logisticsNo;
	}

	public function getLogisticsNo()
	{
		return $this->logisticsNo;
	}

	public function setOutRef($outRef)
	{
		$this->outRef = $outRef;
		$this->apiParas["out_ref"] = $outRef;
	}

	public function getOutRef()
	{
		return $this->outRef;
	}

	public function setSendType($sendType)
	{
		$this->sendType = $sendType;
		$this->apiParas["send_type"] = $sendType;
	}

	public function getSendType()
	{
		return $this->sendType;
	}

	public function setServiceName($serviceName)
	{
		$this->serviceName = $serviceName;
		$this->apiParas["service_name"] = $serviceName;
	}

	public function getServiceName()
	{
		return $this->serviceName;
	}

	public function setTrackingWebsite($trackingWebsite)
	{
		$this->trackingWebsite = $trackingWebsite;
		$this->apiParas["tracking_website"] = $trackingWebsite;
	}

	public function getTrackingWebsite()
	{
		return $this->trackingWebsite;
	}

	public function getApiMethodName()
	{
		return "aliexpress.solution.order.fulfill";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->logisticsNo,"logisticsNo");
		RequestCheckUtil::checkNotNull($this->outRef,"outRef");
		RequestCheckUtil::checkNotNull($this->sendType,"sendType");
		RequestCheckUtil::checkNotNull($this->serviceName,"serviceName");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
