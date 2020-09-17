<?php

/**
 * 商品基本信息列表
 * @author auto create
 */
class AeopAeProductDisplaySampleDto
{
	
	/** 
	 * 卡券商品结束有效期
	 **/
	public $coupon_end_date;
	
	/** 
	 * 卡券商品开始有效期
	 **/
	public $coupon_start_date;
	
	/** 
	 * 货币单位
	 **/
	public $currency_code;
	
	/** 
	 * 产品关联的运费模版ID
	 **/
	public $freight_template_id;
	
	/** 
	 * 产品发布时间。
	 **/
	public $gmt_create;
	
	/** 
	 * 商品最后更新时间 （系统更新时间也会记录）。
	 **/
	public $gmt_modified;
	
	/** 
	 * 图片URL.静态单图主图个数为1,动态多图主图个数为2-6. 多个图片url用‘;’分隔符连接。
	 **/
	public $image_u_r_ls;
	
	/** 
	 * 商品所属人loginId
	 **/
	public $owner_member_id;
	
	/** 
	 * 商品所属人Seq
	 **/
	public $owner_member_seq;
	
	/** 
	 * 商品ID
	 **/
	public $product_id;
	
	/** 
	 * 最大价格。
	 **/
	public $product_max_price;
	
	/** 
	 * 最小价格。
	 **/
	public $product_min_price;
	
	/** 
	 * 产品来源。'tdx'为淘宝代销产品，'1688'为1688分销商品，'isv'为通过API发布的商品。其他字符或空为普通产品。
	 **/
	public $src;
	
	/** 
	 * 产品来源的详情地址，目前仅支持1688
	 **/
	public $src_detail_url;
	
	/** 
	 * 商品标题
	 **/
	public $subject;	
}
?>