<?
namespace Acrit\Core\Export;

use
	\Acrit\Core\Helper,
	\Acrit\Core\Export\Plugins\OzonRuHelpers\AttributeValueTable as AttributeValue;

Helper::loadMessages(__FILE__);

$intShowMax = 1000;
$strQuery = $arGet['query'];
$bSearch = !!strlen($strQuery);
if($bSearch && !Helper::isUtf()){
	$strQuery = Helper::convertEncoding($strQuery, 'UTF-8', 'CP1251');
}
$arFilter = [];
$arQuery = preg_split('#[,;\s]+#', $strQuery);
foreach($arQuery as $strText){
	$arFilter[] = ['%VALUE' => $strText];
}

$arFilterAll = [
	'CATEGORY_ID' => $arAttribute['CATEGORY_ID'],
	'ATTRIBUTE_ID' => $arAttribute['ATTRIBUTE_ID'],
];
if($this->isAttributeDictionaryCommon($arAttribute['ATTRIBUTE_ID'])){
	unset($arFilterAll['CATEGORY_ID']);
}
if($bSearch){
	$arFilterSearch = array_merge($arFilterAll, [
		[
			'LOGIC' => 'OR',
			$arFilter,
			['=VALUE_ID' => $strQuery],
		],
	]);
}

# Get full values count for current attribute
$intCountAll = 0;
$arQuery = [
	'filter' => $arFilterAll,
	'select' => ['CNT'],
	'runtime' => [
		new \Bitrix\Main\Entity\ExpressionField('CNT', 'COUNT(*)'),
	],
];
if($arQueryResult = AttributeValue::getList($arQuery)->fetch()){
	$intCountAll = intVal($arQueryResult['CNT']);
}

#
if(!$bSearch && $intCountAll <= $intShowMax){
	$bSearch = true;
	$arFilterSearch = $arFilterAll;
}

# Get count of found values
$intCountFound = 0;
if($bSearch){
	$arQuery = [
		'filter' => $arFilterSearch,
		'select' => ['CNT'],
		'runtime' => [
			new \Bitrix\Main\Entity\ExpressionField('CNT', 'COUNT(*)'),
		],
	];
	if($arQueryResult = AttributeValue::getList($arQuery)->fetch()){
		$intCountFound = intVal($arQueryResult['CNT']);
	}
}

# Get first N elements
$arFoundItems = [];
if($bSearch){
	$arQuery = [
		'filter' => $arFilterSearch,
		'limit' => $intShowMax,
		'select' => ['VALUE_ID', 'VALUE'],
	];
	$resItems = AttributeValue::getList($arQuery);
	while($arItem = $resItems->fetch()){
		$arFoundItems[$arItem['VALUE_ID']] = $arItem['VALUE'];
	}
}

?>

<?if($bSearch):?>
	<div data-role="allowed-values-found-info">
		<?=static::getMessage('FILTER_INFO', [
			'#COUNT_ALL#' => $intCountAll,
			'#COUNT_FOUND#' => $intCountFound,
			'#COUNT_SHOWN#' => $intShowMax,
		]);?>
	</div>
<?endif?>

<?if(!empty($arFoundItems)):?>
	<div data-role="allowed-values-found-items">
		<?foreach($arFoundItems as $intValue => $strValue):?>
			<span data-id="<?=$intValue;?>"><?=$strValue;?></span>
		<?endforeach?>
	</div>
<?elseif($bSearch):?>
	<?=static::getMessage('FILTER_NOTHING_FOUND');?>
<?else:?>
	<?=static::getMessage('FILTER_GREETING', ['#COUNT_ALL#' => $intCountAll]);?>
<?endif?>
