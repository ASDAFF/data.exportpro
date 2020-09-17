<?
$MESS['ACRIT_EXP_STRUCTURE_IBLOCK_SELECT_EMPTY'] = '--- выберите инфоблок ---';
$MESS['ACRIT_EXP_STRUCTURE_IBLOCK_SELECT_MULTIPLE_NOTICE'] = '<b>Внимание!</b> В данном профиле настроено сразу несколько инфоблоков (всего настроено инфоблоков: #COUNT#). <a href="javascript:void(0)" class="acrit-inline-link" data-role="iblock-multiple-notice-toggle">Подробнее</a>
<div data-role="iblock-multiple-notice-container" style="display:none;">
	<p>Выгрузка в данном профиле осуществляется сразу из всех выбранных инфоблоков, причем каждый инфоблок имеет собственные настройки.</p>
	<ul>
		<li>Для перехода к настройкам каждого из них выберите его в списке (см. выше).</li>
		<li>Инфоблоки настраиваются поочередно.</li>
		<li>Настроенные инфоблоки легко найти: их названия начинаются со звездочки (или смотрите <a href="javascript:void(0)" data-role="iblock-multiple-notice-link" style="color:inherit;">просмотр инфоблоков</a>).</li>
		<li>Красный крестик удаляет настройки текущего инфоблока (настройки других инфоблоков не трогаются).</li>
		<li>Данное уведомление можно скрыть в <a href="/bitrix/admin/settings.php?lang=#LANGUAGE_ID#&mid=#MODULE_ID#" target="_blank" style="color:inherit;">настройках модуля</a>.</li>
	</ul>
</div>
<script>
$(\'a[data-role="iblock-multiple-notice-link"]\').bind(\'click\', function(e){
	e.preventDefault();
	$(\'[data-role="preview-iblocks"]\').trigger(\'click\');
});
$(\'a[data-role="iblock-multiple-notice-toggle"]\').bind(\'click\', function(e){
	e.preventDefault();
	var container = $(\'[data-role="iblock-multiple-notice-container"\');
	if(!container.is(\':animated\')){
		container.slideToggle();
	}
});
</script>';
?>