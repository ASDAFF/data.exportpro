<?
namespace Acrit\Core;

use
	\Acrit\Core\Helper;

?>
<fieldset title="<?=Helper::getMessage('ACRIT_CORE_FEEDBACK_TITLE');?>" style="line-height: 140%;">
	<legend><?=Helper::getMessage('ACRIT_CORE_FEEDBACK_TITLE');?></legend>
	<?=Helper::getMessage('ACRIT_CORE_FEEDBACK_TEXT', ['#DOMAIN#' => 'https://www.acrit-studio.ru']);?>
</fieldset>