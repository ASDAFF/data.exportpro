<?
namespace Acrit\Core\Export\Plugins;
use
	\Acrit\Core\Helper;
?>
<div data-role="categories-update-attributes-category">
	<?if($bStart):?>
		<div data-role="categories-update-attributes-start">
			<?=static::getMessage('STATUS_START');?>
		</div>
	<?elseif($arSession['FINISHED']):?>
		<?$obDate = new \Bitrix\Main\Type\Datetime();?>
		<div data-role="categories-update-attributes-finished">
			<?=static::getMessage('STATUS_FINISHED', ['#DATE#' => $obDate->toString()]);?>
		</div>
	<?else:?>
		<div data-role="categories-update-attributes-status-category">
			<?=static::getMessage('STATUS_CATEGORY', [
				'#ID#' => $arSession['CATEGORY_ID'],
				'#NAME#' => $arSession['CATEGORY_NAME'],
				'#INDEX#' => $arSession['INDEX'],
				'#COUNT#' => $arSession['COUNT'],
				'#PERCENT#' => $arSession['COUNT'] > 0 ? 
					round((($arSession['INDEX'] - 1) / $arSession['COUNT']) * 100) : 0,
			]);?>
		</div>
		<?if($arSession['ATTRIBUTE_ID']):?>
			<div data-role="categories-update-attributes-status-attribute">
				<?=static::getMessage('STATUS_ATTRIBUTE', [
					'#ID#' => $arSession['ATTRIBUTE_ID'],
					'#DICTIONARY_ID#' => $arSession['ATTRIBUTE_DICTIONARY_ID'],
					'#NAME#' => $arSession['ATTRIBUTE_NAME'],
					'#INDEX#' => $arSession['ATTRIBUTE_INDEX'],
					'#COUNT#' => $arSession['ATTRIBUTE_COUNT'],
					'#PERCENT#' => $arSession['ATTRIBUTE_COUNT'] > 0 ? 
						round((($arSession['ATTRIBUTE_INDEX'] - 1) / $arSession['ATTRIBUTE_COUNT']) * 100) : 0,
					'#SUB_INDEX#' => number_format($arSession['SUB_INDEX'], 0, '', ' '),
				]);?>
			</div>
		<?endif?>
	<?endif?>
</div>
