<?
namespace Acrit\Core\Export\Plugins;

use
	\Acrit\Core\Helper;

Helper::loadMessages(__FILE__);
$strLang = 'ACRIT_EXP_YANDEX_MARKET_PRICELABS_';
$strHint = $strLang.'HINT_';

?>

<table class="adm-list-table">
	<tbody>
		<tr>
			<td width="40%" style="vertical-align:top;">
				<?=Helper::showHint(Helper::getMessage($strHint.'PARAMS'));?>
				<?=Helper::getMessage($strLang.'PARAMS');?>:
			</td>
			<td width="60%">
				<div>
					<textarea name="iblockparams[<?=$intIBlockID;?>][PRICELABS_PARAMS]" cols="60" rows="8"
						style="min-height:100px; resize:vertical; width:90%;"
						><?=htmlspecialcharsbx($arIBlockParams['PRICELABS_PARAMS']);?></textarea>
				</div>
				<?=Helper::showNote(Helper::getMessage($strLang.'PARAMS_NOTE'), true);?>
			</td>
		</tr>
	</tbody>
</table>
