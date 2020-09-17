<?
// General
$MESS['ACRIT_EXP_PAGE_TITLE'] = 'Миграция профилей из старого ядра модуля в новое';

// Core notice
$MESS['ACRIT_EXP_CORE_NOTICE'] = '<b>Внимание!</b> Не установлен необходимый для работы служебный модуль <a href="/bitrix/admin/update_system_partner.php?addmodule=#CORE_ID#&lang=#LANG#" target="_blank">acrit.core</a>. Установите его для продолжения работы.';

$MESS['ACRIT_EXP_NOTICE'] = '<b style="color:red">Мигратор профилей работает в тестовом режиме!</b><br/><br/>
На этой странице Вы можете перенести профили выгрузки из <a href="/bitrix/admin/#MODULE_UNDERSCORE#_list.php?lang=#LANGUAGE_ID#" target="_blank">старой</a> версии модуля в <a href="/bitrix/admin/#MODULE_UNDERSCORE#_new_list.php?lang=#LANGUAGE_ID#" target="_blank" style="color:green">новую</a>.
<p><b>Внимание!</b> Учтите, что ввиду кардинальных различий между старым ядром и новым, нет возможности полностью автоматической миграции. С помощью данного инструмента Вы можете сделать перенос только части настроек профилей, <b style="color:red">после этого обязательна проверка и донастройка каждого профиля!</b></p>
<p>Что потребуется проверить и донастроить:</p>
<ul>
	<li>все настройки полей и значений,</li>
	<li>фильтры (особенно фильтр по условию "Не выполнено"),</li>
	<li>режим работы торговых предложений,</li>
	<li>дополнительнные поля, добавленные вручную,</li>
	<li>общая конвертация данных,</li>
	<li>конвертирование валют,</li>
	<li>и другое.</li>
</ul>
<p>Каждая повторная миграция одного и того же профиля из старого ядра приводит к созданию нового профиля в новом ядре.</p>
';
$MESS['ACRIT_EXP_SITE_PREFIX'] = 'Сайт';
$MESS['ACRIT_EXP_ALREADY_MIGRATED'] = '<a href="#URL#" target="_blank" style="color:green;font-style:italic;">уже мигрирован! (ID=#ID#)</a>';
$MESS['ACRIT_EXP_SELECT_ALL'] = 'Выбрать все';
$MESS['ACRIT_EXP_SELECT_NONE'] = 'Отменить выбор';
$MESS['ACRIT_EXP_SELECT_INVERT'] = 'Инвертировать выбор';
$MESS['ACRIT_EXP_MIGRATE_BUTTON'] = 'Выполнить миграцию';

// Popup: backup restore
$MESS['ACRIT_EXP_TAB_GENERAL_NAME'] = 'Профили';
$MESS['ACRIT_EXP_TAB_GENERAL_DESC'] = 'Выберите профили для перевода на новое ядро';

$MESS['ACRIT_EXP_NO_MIGRATABLE_PROFILES'] = 'Нет профилей, которые можно мигрировать в новое ядро.';

?>