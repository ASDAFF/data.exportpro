<?php

/**
 * Screening result creation request
 * @author auto create
 */
class RmaScreeningCreationRequest
{
	
	/** 
	 * Values: OK, NO_OK
	 **/
	public $result;
	
	/** 
	 * RMA ID
	 **/
	public $rma_id;
	
	/** 
	 * Date of screening
	 **/
	public $screening_date;
	
	/** 
	 * Description of the screening result
	 **/
	public $screening_result_details;
	
	/** 
	 * Values: CUSTOMER_FAULT, GIVE_UP_UNSEALED, GIVE_UP_SEALED, DOA_SEALED_QUALITY_ISSUE, DOA_SEALED_NO_QUALITY_ISSUE
	 **/
	public $screening_result_reasons;	
}
?>