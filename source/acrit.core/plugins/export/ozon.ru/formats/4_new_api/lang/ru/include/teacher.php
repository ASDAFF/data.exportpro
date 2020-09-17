<?
\Acrit\Core\Export\Exporter::getLangPrefix(realpath(__DIR__.'/../class.php'), $strLang, $strHead, $strName, $strHint);
$strTLang = $strLang.'TEACHER_'; // Teacher lang general
$strTSNLang = $strTLang.'STEP_NAME_'; // Lang for step name
$strTSDLang = $strTLang.'STEP_DESC_'; // Lang for step description

$MESS[$strTLang.'NAME'] = 'Настройка выгрузки OZON';
$MESS[$strTLang.'TITLE'] = 'Информация об особенностях формата выгрузки «#NAME#»';
$MESS[$strTLang.'SPLASH_SCREEN_DESCRIPTION'] = 'В этом уроке мы покажем особенности настройки плагина OZON. При этом будут показаны только те аспекты настройки, которые относятся к Ozon.<br/><br/>
Перед прохождением этого урока рекомендуем пройти урок по общей настройке профилей.';

$MESS[$strTSNLang.'OZON_AUTH_DATA'] = 'Укажите данные для авторизации (из личного кабинета).';
	$MESS[$strTSDLang.'OZON_AUTH_DATA'] = 'Данные для авторизации необходимо скопировать из <a href="https://seller.ozon.ru/settings/api-keys" target="_blank">личного кабинета</a>.<br/>
	После указания данных нажмите кнопку «Проверить доступ», чтобы убедиться, что данные введены корректно.';
$MESS[$strTSNLang.'OZON_DESCRIPTION_HOWTO'] = 'Ознакомьтесь с общим порядком начала работы - это рекомендации Ozon.';
$MESS[$strTSNLang.'OZON_DESCRIPTION_RECOMMENDATIONS'] = 'Внимательно прочитайте наши рекомендации.';
	$MESS[$strTSDLang.'OZON_DESCRIPTION_RECOMMENDATIONS'] = 'Эти рекомендации - залог успешной настройки. В нашем модуле экспорта выгрузка в Ozon - одна из наиболее сложных задач, поэтому соблюдайте рекомендации для более лёгкой настройки.';
$MESS[$strTSNLang.'OZON_DESCRIPTION_NUANCES'] = 'Также, прочитайте о важных и неочевидных нюансах - это поможет Вам сэкономить время при работе.';
$MESS[$strTSNLang.'SUBTAB_CATEGORIES'] = 'Переходим к настройке категорий';
$MESS[$strTSNLang.'OZON_CATEGORY_REDEFINITIONS_BUTTON'] = 'Это кнопка для управления соответствий категорий сайта категориям Ozon';
$MESS[$strTSNLang.'OZON_CATEGORY_REDEFINITIONS_POPUP'] = 'Здесь необходимо для каждого раздела выбрать соответствие. Это можно сделать после окончания работы мастера.';
$MESS[$strTSNLang.'OZON_CATEGORY_ATTRIBUTES_UPDATE'] = 'Обновление справочников - обязательно, т.к. справочники атрибутов пополняются ежедневно. Это также можно сделать после окончания работы мастера.';
	$MESS[$strTSDLang.'OZON_CATEGORY_ATTRIBUTES_UPDATE'] = 'Обновление справочников необходимо выполнять после изменения набора категорий.<br/>
	Имейте ввиду, это - длительный процесс, только для одной категории он может занимать несколько десятков минут!';
$MESS[$strTSNLang.'OZON_ALLOWED_VALUES'] = 'Восклицательный знак обозначает что данное поле обязано содержать только те значения, которые определены справочником.';
	$MESS[$strTSDLang.'OZON_ALLOWED_VALUES'] = 'Клик по восклицательному знаку открывает окно со списком возможных значений.';
$MESS[$strTSNLang.'OZON_ALLOWED_VALUES_POPUP'] = 'Здесь представлен список допустимых значений. Клик по каждому значению копирует текст в буфер обмена, чтобы его можно было удобно скопировать в поле.';
	$MESS[$strTSDLang.'OZON_ALLOWED_VALUES_POPUP'] = 'Напомним, что для отображения актуальных данных в этом всплывающем окне необходимо обновить справочники категорий.';
$MESS[$strTSNLang.'OZON_TASKS_LOG'] = 'Здесь представлен список выполненных задач. Эта информация крайне полезна для отладки.';
	$MESS[$strTSDLang.'OZON_TASKS_LOG'] = '';



?>