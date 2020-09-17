<?
namespace Acrit\Core\Export;

use
	\Acrit\Core\Helper,
	\Acrit\Core\Export\Plugins\OzonRuHelpers\HistoryTable as History;

# Count
print sprintf(static::getMessage('STATUS_COUNT'), $arStatus['Count']);

# Statuses
$intSuccess = 0;
foreach($arStatus['Status'] as $strStatus => $intCount){
	$strStatusUpper = toUpper($strStatus);
	print sprintf(static::getMessage('STATUS_'.$strStatusUpper), $intCount);
	if($strStatusUpper != toUpper('Pending')){
		$intSuccess = $intCount;
	}
}

# More data
$arHistoryItems = [];
if($arTask['ID']){
	$arQuery = [
		'filter' => [
			'PROFILE_ID' => $this->intProfileId,
			'TASK_ID' => $arTask['TASK_ID'],
		],
		'select' => [
			'ID',
			'OFFER_ID',
			'PRODUCT_ID',
			'JSON',
			'STATUS',
			'STATUS_DATETIME',
		],
	];
	$resHistoryItems = History::getList($arQuery);
	while($arHistoryItem = $resHistoryItems->fetch()){
		$arHistoryItems[] = $arHistoryItem;
	}
}
if(!empty($arHistoryItems)){
	?>
		<a data-role="log-tasks-status-toggle" class="acrit-inline-link"><?=static::getMessage('STATUS_TOGGLE');?></a>
		<div data-role="log-tasks-status-details-table">
			<table>
				<thead>
					<tr>
						<th>OfferID</th>
						<th>ProductID</th>
						<th>Status</th>
					</tr>
				</thead>
				<tbody>
					<?foreach($arHistoryItems as $arItem):?>
						<tr>
							<td align="right">
								<a data-role="log-tasks-status-preview" class="acrit-inline-link" 
									data-id="<?=$arItem['ID'];?>"><?=$arItem['OFFER_ID'];?></a>
							</td>
							<td align="right">
								<?=$arItem['PRODUCT_ID'];?>
							</td>
							<td>
								<?=$arItem['STATUS'];?>
							</td>
						</tr>
					<?endforeach?>
				</tbody>
			</table>
		</div>
	<?
}


?>