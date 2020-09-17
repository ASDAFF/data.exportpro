<?
$arStores = [];
if(\Bitrix\Main\Loader::includeModule('catalog') && class_exists('\CCatalogStore')) {
	$resStores = \CCatalogStore::getList(['SORT' => 'ASC', 'ID' => 'ASC']);
	while($arStore = $resStores->getNext()) {
		$arStores[$arStore['ID']] = $arStore['TITLE'];
	}
}

if(!is_array($this->arParams['STORES'])){
	$this->arParams['STORES'] = [$this->arParams['STORES']];
}
?>
<select name="PROFILE[PARAMS][STORES][]" multiple="multiple" size="6" style="min-width:300px;">
	<?foreach($arStores as $intStoreId => $strStoreName):?>
		<?$bSelected = in_array($intStoreId, $this->arParams['STORES']);?>
		<option value="<?=$intStoreId;?>"<?if($bSelected):?>selected="selected"<?endif?>
			><?=$strStoreName;?> [<?=$intStoreId;?>]</option>
	<?endforeach?>
</select>