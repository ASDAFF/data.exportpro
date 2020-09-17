<?php

/**
 * Price list for different countries
 * @author auto create
 */
class SingleCountryPriceDto
{
	
	/** 
	 * ship to country
	 **/
	public $ship_to_country;
	
	/** 
	 * Sku price list under the same ship_to_country
	 **/
	public $sku_price_by_country_list;	
}
?>