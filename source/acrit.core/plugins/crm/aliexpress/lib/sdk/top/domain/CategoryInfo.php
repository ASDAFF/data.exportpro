<?php

/**
 * children category list under category_id
 * @author auto create
 */
class CategoryInfo
{
	
	/** 
	 * category id
	 **/
	public $children_category_id;
	
	/** 
	 * whether the category is leaf or not
	 **/
	public $is_leaf_category;
	
	/** 
	 * level of the categories. As for root categories, the level is 1
	 **/
	public $level;
	
	/** 
	 * multi langauge names of the categories
	 **/
	public $multi_language_names;	
}
?>