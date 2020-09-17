<?
namespace Acrit\Core\Export;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper,
	\Acrit\Core\Export\Filter;

Loc::loadMessages(__FILE__);

$strConsoleCommand = \CUserOptions::GetOption($strModuleId, 'console_command'); // ToDo: move '\CUserOptions::GetOption' to Helper::getUserOption
if(strlen($strConsoleCommand)){
	$strConsoleCommand = base64_decode($strConsoleCommand);
}
$strConsoleHeight = \CUserOptions::GetOption($strModuleId, 'console_height');
$strConsoleText = \CUserOptions::GetOption($strModuleId, 'console_text');

$arCommands = array(
	array(
		'TITLE' => 'findFirst',
		'COMMAND' => 'Debug::findFirst()',
		'TEXT_MODE' => 'N',
		'HINT' => Loc::getMessage('ACRIT_EXP_TAB_CONSOLE_findFirst'),
	),
	array(
		'TITLE' => 'findRandom',
		'COMMAND' => 'Debug::findRandom()',
		'TEXT_MODE' => 'N',
		'HINT' => Loc::getMessage('ACRIT_EXP_TAB_CONSOLE_findRandom'),
	),
	array(
		'TITLE' => 'findSelected',
		'COMMAND' => 'Debug::findSelected(1234567890)',
		'TEXT_MODE' => 'N',
		'HINT' => Loc::getMessage('ACRIT_EXP_TAB_CONSOLE_findSelected'),
	),
	array(
		'TITLE' => 'generateFirst',
		'COMMAND' => 'Debug::generateFirst()',
		'TEXT_MODE' => 'N',
		'HINT' => Loc::getMessage('ACRIT_EXP_TAB_CONSOLE_generateFirst'),
	),
	array(
		'TITLE' => 'generateRandom',
		'COMMAND' => 'Debug::generateRandom()',
		'TEXT_MODE' => 'N',
		'HINT' => Loc::getMessage('ACRIT_EXP_TAB_CONSOLE_generateRandom'),
	),
	array(
		'TITLE' => 'generateSelected',
		'COMMAND' => 'Debug::generateSelected(1234567890)',
		'TEXT_MODE' => 'N',
		'HINT' => Loc::getMessage('ACRIT_EXP_TAB_CONSOLE_generateSelected'),
	),
	array(
		'TITLE' => 'showFilter',
		'COMMAND' => 'Debug::showFilter()',
		'TEXT_MODE' => 'Y',
		'HINT' => Loc::getMessage('ACRIT_EXP_TAB_CONSOLE_showFilter'),
	),
	array(
		'TITLE' => 'showOffersFilter',
		'COMMAND' => 'Debug::showOffersFilter()',
		'TEXT_MODE' => 'Y',
		'HINT' => Loc::getMessage('ACRIT_EXP_TAB_CONSOLE_showOffersFilter'),
	),
	array(
		'TITLE' => 'showFilterSql',
		'COMMAND' => 'Debug::showFilterSql()',
		'TEXT_MODE' => 'Y',
		'HINT' => Loc::getMessage('ACRIT_EXP_TAB_CONSOLE_showFilterSql'),
	),
	array(
		'TITLE' => 'showOffersFilterSql',
		'COMMAND' => 'Debug::showOffersFilterSql()',
		'TEXT_MODE' => 'Y',
		'HINT' => Loc::getMessage('ACRIT_EXP_TAB_CONSOLE_showOffersFilterSql'),
	),
);

?>
<div class="acrit-exp-console">
	<div class="acrit-exp-console-fast-commands">
		<?foreach($arCommands as $arCommand):?>
			<input type="button" value="<?=$arCommand['TITLE'];?>" data-role="console-fast-command" 
				data-command="<?=$arCommand['COMMAND'];?>" data-text="<?=$arCommand['TEXT_MODE'];?>"
				title="<?=$arCommand['HINT'];?>"/>
		<?endforeach?>
	</div>
	<div class="acrit-exp-console-text">
		<textarea data-role="console-text"<?if($strConsoleHeight):?> style="height:<?=$strConsoleHeight;?>px"<?endif?>><?
			print htmlspecialcharsbx($strConsoleCommand);
		?></textarea>
	</div>
	<div class="acrit-exp-console-button">
		<input type="button" value="<?=Loc::getMessage('ACRIT_EXP_TAB_CONSOLE_EXECUTE');?>" data-role="console-execute" />
		&nbsp;
		<label>
			<input type="checkbox" data-role="console-type"<?if($strConsoleText=='Y'):?> checked="checked"<?endif?> />
			<?=Loc::getMessage('ACRIT_EXP_TAB_CONSOLE_TYPE');?>
		</label>
	</div>
	<div class="acrit-exp-console-results" data-role="console-results"></div>
</div>