<?
namespace Acrit\Core\Export\Plugins;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper;

Loc::loadMessages(__FILE__);

$bIBlockParamsDefault = false;
if(is_null($arIBlockParams)){
	$bIBlockParamsDefault = true;
	$arIBlockParams = array(
		'CUSTOM_XML_STRUCTURE_ITEM' => Helper::convertUtf8(file_get_contents(__DIR__.'/../default_xml/item.xml')),
		'CUSTOM_XML_STRUCTURE_OFFER' => Helper::convertUtf8(file_get_contents(__DIR__.'/../default_xml/offer.xml')),
	);
}

?>

<?=Helper::showHeading(Loc::getMessage('ACRIT_EXP_PROFILE_CUSTOM_XML_STRUCTURE_ITEM'), true);?>
<div>
	<div data-role="xml-structure-wrapper">
		<textarea class="acrit-exp-custom-xml-structure" name="iblockparams[<?=$intIBlockID;?>][CUSTOM_XML_STRUCTURE_ITEM]" style="height:400px"><?
			print htmlspecialcharsbx($arIBlockParams['CUSTOM_XML_STRUCTURE_ITEM']);
		?></textarea>
		<?=Helper::showNote($obPlugin::getMessage('CHECK_XML_VALID_NOTICE'), true);?>
		<?if($obPlugin->isOffersPreprocess()):?>
			<?=Helper::showNote(Loc::getMessage('ACRIT_EXP_XML_STRUCTURE_AVAILABLE_MACROS', array('#MACROS#' => '#OFFERS#')), true);?>
		<?endif?>
	</div>
</div><br/>

<?if($intIBlockOffersID):?>
	<?=Helper::showHeading(Loc::getMessage('ACRIT_EXP_PROFILE_CUSTOM_XML_STRUCTURE_OFFER'), true);?>
	<div>
		<div data-role="xml-structure-wrapper">
			<textarea class="acrit-exp-custom-xml-structure" name="iblockparams[<?=$intIBlockID;?>][CUSTOM_XML_STRUCTURE_OFFER]" style="height:400px"><?
				print htmlspecialcharsbx($arIBlockParams['CUSTOM_XML_STRUCTURE_OFFER']);
			?></textarea>
			<?=Helper::showNote($obPlugin::getMessage('CHECK_XML_VALID_NOTICE'), true);?>
		</div>
	</div><br/>
<?endif?>

<?
if($bIBlockParamsDefault){
	$arIBlockParams = null;
}
?>
