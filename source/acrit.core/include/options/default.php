<?
namespace Acrit\Core;

use
	\Acrit\Core\Helper;

$strModuleId = &$arVariables['MODULE_ID'];
$strModuleCodeFull = str_replace('.', '_', $strModuleId);
$arOptions = &$arVariables['OPTIONS'];
$obOptions = &$arVariables['THIS'];

?>
<?foreach($arOptions as $arGroup):?>
	<tr class="heading">
		<td colspan="2"><?=$arGroup['NAME'];?><?if($arGroup['HINT']):?> <?=Helper::showHint($arGroup['HINT']);?><?endif?></td>
	</tr>
	<?foreach($arGroup['OPTIONS'] as $strOption => $arOption):?>
		<?
		$arOption['CODE'] = $strOption;
		$arOption['VALUE'] = $strValue = Helper::getOption($strModuleId, $strOption);
		?>
		<tr id="acrit_exp_option_<?=$strOption;?>">
			<td width="40%"<?if($arOption['TOP'] == 'Y'):?> style="padding-top:10px; vertical-align:top;"<?endif?>>
				<?=Helper::showHint($arOption['HINT']);?>
				<label for="<?=$strModuleCodeFull;?>_option_<?=$strOption;?>">
					<?if($arOption['REQUIRED']):?>
						<b><?=$arOption['NAME'];?></b>:
					<?else:?>
						<?=$arOption['NAME'];?>:
					<?endif?>
				</label>
			</td>
			<td width="40%">
				<?
				if(is_callable($arOption['CALLBACK_MAIN'])){
					call_user_func_array($arOption['CALLBACK_MAIN'], [$obOptions, $arOption]);
				}
				else{
					switch($arOption['TYPE']) {
						case 'text':
							?>
							<input type="text" name="<?=$strOption;?>" value="<?=$strValue;?>" <?=$arOption['ATTR'];?> 
								id="<?=$strModuleCodeFull;?>_option_<?=$strOption;?>" />
							<?
							break;
						case 'password':
							?>
							<input type="password" name="<?=$strOption;?>" value="<?=$strValue;?>" <?=$arOption['ATTR'];?> 
								id="<?=$strModuleCodeFull;?>_option_<?=$strOption;?>" />
							<?
							break;
						case 'textarea':
							?>
							<textarea name="<?=$strOption;?>" <?=$arOption['ATTR'];?>
								id="<?=$strModuleCodeFull;?>_option_<?=$strOption;?>"><?=$strValue;?></textarea>
							<?
							break;
						case 'checkbox':
							if(stripos($arOption['ATTR'], 'disabled') !== false){
								$strValue = 'N';
							}
							?>
							<input type="hidden" name="<?=$strOption;?>" value="N" />
							<input type="checkbox" name="<?=$strOption;?>" value="Y" <?=$arOption['ATTR'];?>
								id="<?=$strModuleCodeFull;?>_option_<?=$strOption;?>"
								<?if($strValue=='Y'):?> checked="checked"<?endif?> />
							<?
							break;
					}
				}
				if(is_callable($arOption['CALLBACK_MORE'])){
					print call_user_func_array($arOption['CALLBACK_MORE'], [$obOptions, $arOption]);
				}
				?>
			</td>
		</tr>
		<?
		if(is_callable($arOption['CALLBACK_BOTTOM'])){
			print call_user_func_array($arOption['CALLBACK_BOTTOM'], [$obOptions, $arOption]);
		}
		?>
	<?endforeach?>
<?endforeach?>