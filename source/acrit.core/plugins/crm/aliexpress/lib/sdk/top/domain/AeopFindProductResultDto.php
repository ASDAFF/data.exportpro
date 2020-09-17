<?php

/**
 * result
 * @author auto create
 */
class AeopFindProductResultDto
{
	
	/** 
	 * 每增加件数.取值范围1-1000。
	 **/
	public $add_unit;
	
	/** 
	 * 对应增加的重量.取值范围:0.001-500.000,保留三位小数,采用进位制,单位:公斤。
	 **/
	public $add_weight;
	
	/** 
	 * 商品多媒体信息，该属性主要包含商品的视频列表
	 **/
	public $aeop_a_e_multimedia;
	
	/** 
	 * 商品的类目属性
	 **/
	public $aeop_ae_product_propertys;
	
	/** 
	 * 商品的SKU信息
	 **/
	public $aeop_ae_product_s_k_us;
	
	/** 
	 * 商品分国家定价规则数据，建议使用新格式，请参考：https://developers.aliexpress.com/doc.htm?docId=109575&docType=1
	 **/
	public $aeop_national_quote_configuration;
	
	/** 
	 * 自定义计重的基本产品件数
	 **/
	public $base_unit;
	
	/** 
	 * 产品的批发折扣
	 **/
	public $bulk_discount;
	
	/** 
	 * 享受批发价的产品数
	 **/
	public $bulk_order;
	
	/** 
	 * 产品所在类目的ID
	 **/
	public $category_id;
	
	/** 
	 * 卡券商品结束有效期
	 **/
	public $coupon_end_date;
	
	/** 
	 * 卡券商品开始有效期
	 **/
	public $coupon_start_date;
	
	/** 
	 * 产品的货币单位。美元: USD, 卢布: RUB
	 **/
	public $currency_code;
	
	/** 
	 * 商品的备货期
	 **/
	public $delivery_time;
	
	/** 
	 * 商品详描
	 **/
	public $detail;
	
	/** 
	 * 错误代码
	 **/
	public $error_code;
	
	/** 
	 * 报错信息
	 **/
	public $error_message;
	
	/** 
	 * 产品关联的运费模版ID
	 **/
	public $freight_template_id;
	
	/** 
	 * 创建日期
	 **/
	public $gmt_create;
	
	/** 
	 * 修改日期
	 **/
	public $gmt_modified;
	
	/** 
	 * 产品的毛重
	 **/
	public $gross_weight;
	
	/** 
	 * 产品所关联的产品分组ID
	 **/
	public $group_id;
	
	/** 
	 * 产品所在的产品分组列表
	 **/
	public $group_ids;
	
	/** 
	 * 产品的主图列表
	 **/
	public $image_u_r_ls;
	
	/** 
	 * 是否是动态图产品
	 **/
	public $is_image_dynamic;
	
	/** 
	 * 是否支持是自定义计重
	 **/
	public $is_pack_sell;
	
	/** 
	 * 关键字
	 **/
	public $keyword;
	
	/** 
	 * 每包的数量
	 **/
	public $lot_num;
	
	/** 
	 * 商品无线详描
	 **/
	public $mobile_detail;
	
	/** 
	 * 商品拥有者的login_id
	 **/
	public $owner_member_id;
	
	/** 
	 * 商品拥有者的ID
	 **/
	public $owner_member_seq;
	
	/** 
	 * 产品的高度
	 **/
	public $package_height;
	
	/** 
	 * 产品的长度
	 **/
	public $package_length;
	
	/** 
	 * 打包销售: true 非打包销售:false
	 **/
	public $package_type;
	
	/** 
	 * 产品的宽度
	 **/
	public $package_width;
	
	/** 
	 * 产品ID
	 **/
	public $product_id;
	
	/** 
	 * productMoreKeywords1
	 **/
	public $product_more_keywords1;
	
	/** 
	 * productMoreKeywords2
	 **/
	public $product_more_keywords2;
	
	/** 
	 * 单品产品的价格。
	 **/
	public $product_price;
	
	/** 
	 * 产品的状态，包括onSelling（正在销售），offline（已下架），auditing（审核中），editingRequired（审核不通过）
	 **/
	public $product_status_type;
	
	/** 
	 * 产品的单位
	 **/
	public $product_unit;
	
	/** 
	 * 产品所关联的服务模版
	 **/
	public $promise_template_id;
	
	/** 
	 * 库存的扣减策略
	 **/
	public $reduce_strategy;
	
	/** 
	 * 产品所关联的尺码模版ID
	 **/
	public $sizechart_id;
	
	/** 
	 * 产品的来源
	 **/
	public $src;
	
	/** 
	 * 产品的标题
	 **/
	public $subject;
	
	/** 
	 * 接口调用结果
	 **/
	public $success;
	
	/** 
	 * 商品概要
	 **/
	public $summary;
	
	/** 
	 * 产品的下架原因，包括user_offline：手动下架，expire_offline：到期下架，punish_offline：网规处罚下架，violate_offline：交易违规下架，degrade_offline：降级下架，industry_offline：未续约下架
	 **/
	public $ws_display;
	
	/** 
	 * 产品的下架日期
	 **/
	public $ws_offline_date;
	
	/** 
	 * 产品的有效期
	 **/
	public $ws_valid_num;	
}
?>