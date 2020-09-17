<?php

/**
 * result
 * @author auto create
 */
class GlobalAeopFindProductResultDto
{
	
	/** 
	 * Required when is_pack_sell equals to true. Value range for pieces to be added: 1-1000. Please refer to the field is_pack_sell for details.
	 **/
	public $add_unit;
	
	/** 
	 * Required when is_pack_sell equals to true. It means weight to be correspondingly added. Range value: 0.001-500.000, reserving three decimal places and applying scale; Unit: kilogram(s). Please refer to the field is_pack_sell for details.
	 **/
	public $add_weight;
	
	/** 
	 * Multimedia information
	 **/
	public $aeop_a_e_multimedia;
	
	/** 
	 * Product properties
	 **/
	public $aeop_ae_product_propertys;
	
	/** 
	 * List for multiple skus of the product, expressed in json format.
	 **/
	public $aeop_ae_product_s_k_us;
	
	/** 
	 * Required when is_pack_sell equals to true. It means no additional freight will be charged when the number of pieces to be purchased is under the base unit. Value range: 1-1000.
	 **/
	public $base_unit;
	
	/** 
	 * Bulk discount for wholesale. It must be multiplied by 100 and selected and saved as integer. Value range: 1-99. Note: It is discount, other than discount rate. For example, if the discount is 68, it should be selected and saved as 32. bulk_order and bulk_discount must have value or have no value simultaneously.
	 **/
	public $bulk_discount;
	
	/** 
	 * Minimum bulk for wholesale. Value range: 2-100000. bulk_order and bulk_discount must have value or have no value simultaneously.
	 **/
	public $bulk_order;
	
	/** 
	 * Product category ID. It must be leaf category to be obtained from the category interface.
	 **/
	public $category_id;
	
	/** 
	 * the Currency code. "USD" will be used as the default value if this information is not provided; Currency code is mandatory for Russian sellers and Spanish sellers. For Russian sellers, RUB should be filled in while EUR for Spanish sellers.
	 **/
	public $currency_code;
	
	/** 
	 * Stocking period. Value range: 1-60; Unit: day(s). Referring to the preparation time before the order could be dispatched.
	 **/
	public $delivery_time;
	
	/** 
	 * Deprecated, please use multi_language_description_list
	 **/
	public $detail;
	
	/** 
	 * shipping template id
	 **/
	public $freight_template_id;
	
	/** 
	 * created time
	 **/
	public $gmt_create;
	
	/** 
	 * modified time
	 **/
	public $gmt_modified;
	
	/** 
	 * Product gross weight. Range value: 0.001-500.000, reserving three decimal places and applying scale; Unit: kilogram(s).
	 **/
	public $gross_weight;
	
	/** 
	 * Group ID that the product belongs to.
	 **/
	public $group_id;
	
	/** 
	 * All the groups that the product belongs to.
	 **/
	public $group_ids;
	
	/** 
	 * image urls for the product
	 **/
	public $image_u_r_ls;
	
	/** 
	 * Whether customized weighting is enabled or not. True means customized weighting enabled. When is_pack_sell equals to true, add_unit, add_weight and base_unit must be filled in. For example, base_unit equals to 5, add_unit equals to 2 and add_weight equals to 1.2. It means that Basic shipping cost will cover the first 5 pieces, freight calculating of which will base on a single product. For every additional 2 pieces, the shipping cost will be added to the total shipping cost for 1.2kg
	 **/
	public $is_pack_sell;
	
	/** 
	 * Number of piece(s) in each pack. In case of packing sale,lotNum>1, and in case of unpacking sale, lotNum=1.
	 **/
	public $lot_num;
	
	/** 
	 * mobile detail
	 **/
	public $mobile_detail;
	
	/** 
	 * multi country price configuration
	 **/
	public $multi_country_price_configuration;
	
	/** 
	 * multo language description list
	 **/
	public $multi_language_description_list;
	
	/** 
	 * multi language subject list
	 **/
	public $multi_language_subject_list;
	
	/** 
	 * Out of date, please ignore
	 **/
	public $owner_member_id;
	
	/** 
	 * Out of date, please ignore.
	 **/
	public $owner_member_seq;
	
	/** 
	 * package height
	 **/
	public $package_height;
	
	/** 
	 * package length
	 **/
	public $package_length;
	
	/** 
	 * Packing sale: true; Unpacking sale: false.
	 **/
	public $package_type;
	
	/** 
	 * package width
	 **/
	public $package_width;
	
	/** 
	 * product ID
	 **/
	public $product_id;
	
	/** 
	 * Price for product
	 **/
	public $product_price;
	
	/** 
	 * product status type
	 **/
	public $product_status_type;
	
	/** 
	 * Product unit
	 **/
	public $product_unit;
	
	/** 
	 * Service template ID which the product is associated with
	 **/
	public $promise_template_id;
	
	/** 
	 * Stock reduction strategy. It is divided into 2 types: stock reduction after placing order (place_order_withhold) or stock reduction after payment (payment_success_deduct).
	 **/
	public $reduce_strategy;
	
	/** 
	 * Size chart template ID that the product is associated with.
	 **/
	public $sizechart_id;
	
	/** 
	 * Deprecated, please use multi_language_subject_list
	 **/
	public $subject;
	
	/** 
	 * The offline date of the product
	 **/
	public $ws_offline_date;	
}
?>