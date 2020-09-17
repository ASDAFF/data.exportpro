<?
namespace Acrit\Core\Export;

use
	\Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper;

Loc::loadMessages(__FILE__);

return [
	'NAME' => Loc::getMessage('ACRIT_CORE_OPTION_GROUP_DISCOUNT_RECALCULATION'),
	'HINT' => Loc::getMessage('ACRIT_CORE_OPTION_GROUP_DISCOUNT_RECALCULATION_HINT'),
	'OPTIONS' => [
		'discount_recalculation_enabled' => [
			'NAME' => Loc::getMessage('ACRIT_CORE_OPTION_DISCOUNT_RECALCULATION_ENABLED'),
			'HINT' => Loc::getMessage('ACRIT_CORE_OPTION_DISCOUNT_RECALCULATION_ENABLED_HINT'),
			'TYPE' => 'checkbox',
			'HEAD_DATA' => function(){
				?>
				<script>
				$(document).delegate('tr#acrit_exp_option_discount_recalculation_enabled input[type=checkbox]', 'change', function(e){
					$('tr#acrit_exp_option_discount_recalculation_calc_value').toggle($(this).is(':checked') && !$(this).is('[disabled]'));
					$('tr#acrit_exp_option_discount_recalculation_calc_discount').toggle($(this).is(':checked') && !$(this).is('[disabled]'));
					$('tr#acrit_exp_option_discount_recalculation_calc_percent').toggle($(this).is(':checked') && !$(this).is('[disabled]'));
					$('tr#acrit_exp_option_discount_recalculation_prices').toggle($(this).is(':checked') && !$(this).is('[disabled]'));
					$('tr#acrit_exp_option_discount_recalculation_iblocks').toggle($(this).is(':checked') && !$(this).is('[disabled]'));
				});
				</script>
				<?
			},
			'CALLBACK_SAVE' => function($obOptions, $arOption){
				\Acrit\Core\DiscountRecalculation::handleSaveOptions();
			},
		],
		'discount_recalculation_calc_value' => [
			'NAME' => Loc::getMessage('ACRIT_CORE_OPTION_DISCOUNT_RECALCULATION_CALC_VALUE'),
			'HINT' => Loc::getMessage('ACRIT_CORE_OPTION_DISCOUNT_RECALCULATION_CALC_VALUE_HINT'),
			'TYPE' => 'checkbox',
		],
		'discount_recalculation_calc_discount' => [
			'NAME' => Loc::getMessage('ACRIT_CORE_OPTION_DISCOUNT_RECALCULATION_CALC_DISCOUNT'),
			'HINT' => Loc::getMessage('ACRIT_CORE_OPTION_DISCOUNT_RECALCULATION_CALC_DISCOUNT_HINT'),
			'TYPE' => 'checkbox',
		],
		'discount_recalculation_calc_percent' => [
			'NAME' => Loc::getMessage('ACRIT_CORE_OPTION_DISCOUNT_RECALCULATION_CALC_PERCENT'),
			'HINT' => Loc::getMessage('ACRIT_CORE_OPTION_DISCOUNT_RECALCULATION_CALC_PERCENT_HINT'),
			'TYPE' => 'checkbox',
		],
		'discount_recalculation_calc_dates' => [
			'NAME' => Loc::getMessage('ACRIT_CORE_OPTION_DISCOUNT_RECALCULATION_CALC_DATES'),
			'HINT' => Loc::getMessage('ACRIT_CORE_OPTION_DISCOUNT_RECALCULATION_CALC_DATES_HINT'),
			'TYPE' => 'checkbox',
		],
		'discount_recalculation_prices' => [
			'NAME' => Loc::getMessage('ACRIT_CORE_OPTION_DISCOUNT_RECALCULATION_PRICES'),
			'HINT' => Loc::getMessage('ACRIT_CORE_OPTION_DISCOUNT_RECALCULATION_PRICES_HINT'),
			'CALLBACK_MAIN' => function($obOptions, $arOption){
				$arCurrentPrices = array_filter(explode(',', $arOption['VALUE']));
				?>
				<select name="<?=$arOption['CODE'];?>[]" multiple="multiple" size="5" style="min-width:200px;">
					<?foreach(Helper::getPriceList() as $arPrice):?>
						<option value="<?=$arPrice['ID'];?>"<?if(in_array($arPrice['ID'], $arCurrentPrices)):?> selected="selected"<?endif?>>[<?=$arPrice['ID'];?>, <?=$arPrice['NAME'];?>] <?=$arPrice['NAME_LANG'];?></option>
					<?endforeach?>
				</select>
				<?
			},
			'TOP' => 'Y',
			'REQUIRED' => true,
		],
		'discount_recalculation_iblocks' => [
			'NAME' => Loc::getMessage('ACRIT_CORE_OPTION_DISCOUNT_RECALCULATION_IBLOCKS'),
			'HINT' => Loc::getMessage('ACRIT_CORE_OPTION_DISCOUNT_RECALCULATION_IBLOCKS_HINT'),
			'CALLBACK_MAIN' => function($obOptions, $arOption){
				print Helper::getHtmlObject(ACRIT_CORE, null, 'iblock_tree', 'default', [
					'CODE' => $arOption['CODE'],
					'VALUE' => $arOption['VALUE'],
					'MULTIPLE' => true,
					'SIZE' => 10,
					'MIN_WIDTH' => 350,
					'JUST_CATALOGS' => true,
				]);
			},
			'TOP' => 'Y',
			'REQUIRED' => true,
		],
	],
];
	
?>