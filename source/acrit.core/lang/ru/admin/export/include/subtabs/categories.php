<?
$MESS['ACRIT_EXP_TAB_SECTIONS_MODE'] = 'Режим выбранных разделов';
	$MESS['ACRIT_EXP_TAB_SECTIONS_MODE_HINT'] = 'Укажите, какой режим следует использовать при работе с разделами.<br/><br/>Т.е. можно отбирать товары из всех имеющихся разделов текущего инфоблока, можно отбирать только из выбранных в списке и т.д.<br/><br/>Также, в соответствии с этим параметром показываются разделы во всплывающем окне переопределения названий разделов.<br/><br/>
<b>Внимание!</b> Если Вы в списке разделов выбираете все доступные разделы, то нет смысла выбирать вариант «Выбранные разделы с подразделами» - это сильно снижает производительность и увеличивает нагрузку на базу данных, т.к. для всех выбранных разделов модуль напрасно пытается найти подразделы.';
	$MESS['ACRIT_EXP_TAB_SECTIONS_MODE_ALL'] = 'Все разделы';
	$MESS['ACRIT_EXP_TAB_SECTIONS_MODE_SELECTED'] = 'Только выбранные, без подразделов';
	$MESS['ACRIT_EXP_TAB_SECTIONS_MODE_SELECTED_WITH_SEBSECTIONS'] = 'Выбранные разделы с подразделами';
$MESS['ACRIT_EXP_TAB_CATEGORIES_LIST'] = 'Выгружать товары из следующих разделов';
	$MESS['ACRIT_EXP_TAB_CATEGORIES_LIST_HINT'] = 'Для режимов «Только выбранные, без подразделов» и «Выбранные разделы с подразделами» имеется возможность указать конкретные разделы.<br/><br/>В <a href="/bitrix/admin/settings.php?mid=#MODULE_ID#&lang=#LANGUAGE_ID#" target="_blank">настройках модуля</a> можно указать, сколько уровней вложенности нужно показывать в данном списке.';
	$MESS['ACRIT_EXP_TAB_CATEGORIES_LIST_UNSELECT'] = 'Снять выделение';
	$MESS['ACRIT_EXP_TAB_CATEGORIES_LIST_UNSELECT_CONFIRM'] = 'Вы уверены, что хотите отменить выделение категорий в списке?';
$MESS['ACRIT_EXP_TAB_CATEGORIES_REDEFINITION_MODE'] = 'Режим названий категорий:';
	$MESS['ACRIT_EXP_TAB_CATEGORIES_REDEFINITION_MODE_HINT'] = 'Модуль предоставляет возможность использовать либо имеющиеся разделы для выгрузки, либо использовать категории, предоставляемые онлайн-сервисом (плагином) - в таком случае нужно вручную для каждого раздела указать сопоставление с разделом сервиса.<br/><br/><b>В режиме «Использовать категории торговой площадки», если этот режим обязателен, важно задать соответствия названий для всех разделов!</b> Иначе по каждому некорректному разделу на сервисе будет возникать ошибка.<br/><br/>Обратите внимание, что задать соответствия можно и в режиме «Использовать категории сайта» - в таком случае это работает только как переименование разделов, не более.<br/><br/><b>Внимание!</b> Данный параметр применяется сразу ко всему профиля, а не к каждому инфоблоку в отдельности, т.к. данная настройка должна быть одинаковой для всех инфоблоков в профиле.';
	$MESS['ACRIT_EXP_TAB_CATEGORIES_REDEFINITION_MODE_STRICT'] = 'Использовать категории торговой площадки';
	$MESS['ACRIT_EXP_TAB_CATEGORIES_REDEFINITION_MODE_CUSTOM'] = 'Использовать категории сайта';
$MESS['ACRIT_EXP_TAB_CATEGORIES_REDEFINITION_SOURCE'] = 'Источник названий категорий:';
	$MESS['ACRIT_EXP_TAB_CATEGORIES_REDEFINITION_SOURCE_HINT'] = 'Выберите, каким способом будут определяться названия категорий.';
	$MESS['ACRIT_EXP_TAB_CATEGORIES_REDEFINITION_SOURCE_HINT_CUSTOM'] = $MESS['ACRIT_EXP_TAB_CATEGORIES_REDEFINITION_SOURCE_HINT'].'<br/><br/>
	<b>Внимание!</b> При выборе значения «Использовать поля товаров» выбранный режим будет применяться и для всех других инфоблоков (в пределах текущего профиля), даже если в них не выбрано соответствующее значение.<br/><br/>
	Таким образом, если в одном профиле хотя бы для одного инфоблока выбран режим «Использовать поля товаров» - он будет использоваться для всех инфоблоков в профиле.<br/><br/>
	В режиме «Использовать поля товаров» в список полей добавляется отдельное поле, в котором нужно выгружать название категории.';
	$MESS['ACRIT_EXP_TAB_CATEGORIES_REDEFINITION_SOURCE_REDEFINITIONS'] = 'Задавать вручную (по умолчанию)';
	$MESS['ACRIT_EXP_TAB_CATEGORIES_REDEFINITION_USER_FIELDS'] = 'Использовать свойства разделов';
	$MESS['ACRIT_EXP_TAB_CATEGORIES_REDEFINITION_CUSTOM'] = 'Использовать поля товаров';
$MESS['ACRIT_EXP_TAB_CATEGORIES_REDEFINITION_SOURCE_UF'] = 'Свойство раздела';
	$MESS['ACRIT_EXP_TAB_CATEGORIES_REDEFINITION_SOURCE_UF_HINT'] = 'Выберите свойство раздела, в котором хранится его название для выгрузки.<br/><br/>
Таким образом, если для раздела заполнено данное свойство, именно оно и будет выгружаться как название раздела, иначе будет выгружать стандартное название раздела.';
	$MESS['ACRIT_EXP_TAB_CATEGORIES_REDEFINITION_SOURCE_UF_EMPTY'] = '--- Выберите свойство ---';
$MESS['ACRIT_EXP_TAB_CATEGORIES_REDEFINITION_MANAGE'] = 'Управление названиями категорий:';
	$MESS['ACRIT_EXP_TAB_CATEGORIES_REDEFINITION_MANAGE_BUTTON'] = 'Настроить названия';
	$MESS['ACRIT_EXP_TAB_CATEGORIES_REDEFINITION_MANAGE_HINT'] = 'Механизм настройки названий позволяет выгружать категории сайта под другими именами.<br/><br/>Это работает как для строгого режима категорий (когда конкретная торговая площадка требует использования только конкретно заданных категорий [напр., google merchant]), но и для произвольного - таким образом можно виртуально переименовать категории сайта.';
$MESS['ACRIT_EXP_TAB_CATEGORIES_EXPORT_PARENTS'] = 'Выгружать родительские категории:';
	$MESS['ACRIT_EXP_TAB_CATEGORIES_EXPORT_PARENTS_HINT'] = 'Данная опция позволяет выгружать родительские категории.<br/><br/>Например (<b>данный пример условен!</b>), если без данной опции категории
	<ul>
		<li>Квартиры,</li>
		<li>Легковые автомобили</li>
	</ul>
	будут выгружать как 
	<ul>
		<li>Квартиры,</li>
		<li>Легковые автомобили,</li>
	</ul>
	то при выгрузке родительских категорий будут выгружены категории
	<ul>
		<li>Недвижимость (родительская категория для "Квартиры"),</li>
		<li>Квартиры,</li>
		<li>Автомобили (родительская категория для "Легковые автомобили"),</li>
		<li>Легковые автомобили.</li>
	</ul>
	<br/><b>Внимание!</b> Данный параметр применяется сразу ко всему профилю, а не к каждому инфоблоку в отдельности.';
$MESS['ACRIT_EXP_TAB_CATEGORIES_UPDATE'] = 'Обновление категорий:';
	$MESS['ACRIT_EXP_TAB_CATEGORIES_UPDATE_HINT'] = 'Для корректной работы выгрузки необходимо периодически обновлять категории, т.к. их набор может немного изменяться.';
$MESS['ACRIT_EXP_TAB_CATEGORIES_UPDATE_BUTTON'] = 'Обновить сейчас!';
$MESS['ACRIT_EXP_TAB_CATEGORIES_UPDATE_DATE'] = 'Дата последнего обновления: ';
?>