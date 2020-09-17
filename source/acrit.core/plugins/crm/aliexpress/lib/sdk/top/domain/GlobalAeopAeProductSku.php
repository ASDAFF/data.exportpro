<?php

/**
 * List for multiple skus of the product, expressed in json format.
 * @author auto create
 */
class GlobalAeopAeProductSku
{
	
	/** 
	 * List of SKU attributes
	 **/
	public $aeop_s_k_u_property_list;
	
	/** 
	 * all of warehouse goods will return barcode
	 **/
	public $barcode;
	
	/** 
	 * The Currency code. "USD" will be used as the default value if this information is not provided; Currency code is mandatory for Russian sellers(RUB) and Spanish sellers(EUR).
	 **/
	public $currency_code;
	
	/** 
	 * SKU ID. Can uniquely represent a SKU within a product range.
	 **/
	public $id;
	
	/** 
	 * Ranges from 1 to 999999 for one sku. The total stock of the entire product within multiple skus should also be in the range of 1 to 999999.
	 **/
	public $ipm_sku_stock;
	
	/** 
	 * Sku merchant code from the seller's system. Format: alphanumeric, length 20, does not contain spaces greater than and less than sign. If you only fill in the product price and product code, you need to create a complete SKU record submission, otherwise the product code can not be saved. The system will consider that only the retail price is submitted, but no SKU, resulting in product editing is not saved.
	 **/
	public $sku_code;
	
	/** 
	 * SKU discount price, also called sale price, value range: 0.01 - 100000.
	 **/
	public $sku_discount_price;
	
	/** 
	 * Sku price. Value range: 0.01-100000; Such as: 200.07 means 200 US dollars 7 cents(if other currency_code is used, referring to the corresponding price in that currency, e.g., 200.07 Euros).
	 **/
	public $sku_price;
	
	/** 
	 * True means stock available for the sku, false means out of stock. The stock of at least one should be available.
	 **/
	public $sku_stock;	
}
?>