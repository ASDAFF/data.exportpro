<?
$strMessPrefix = 'ACRIT_EXP_YANDEX_TURBO_GENERAL_';

$MESS[$strMessPrefix.'GENERAL_PURPOSE'] = 'Данный формат предназначен для выгрузки статей и других подобных данных в сервис Яндекс.Турбо.';

$MESS[$strMessPrefix.'GENERAL_DESCRIPTION'] = '<p><b>Внимание!</b> Имейте ввиду, что для выгрузки действует ограничение на количество записей в одном файле, поэтому в результате выгрузки может получиться несколько файлов.</p>';

//
$MESS[$strMessPrefix.'USEFUL_LINKS'] = 'Полезные ссылки';
	$MESS[$strMessPrefix.'ABOUT'] = 'О технологии «Турбо-страницы»';
	$MESS[$strMessPrefix.'QUICK_START'] = 'Как начать работу';
	$MESS[$strMessPrefix.'EXAMPLE'] = 'Пример создания простой Турбо-страницы';
	$MESS[$strMessPrefix.'RSS_ELEMENTS'] = 'Описание RSS-элементов';
	$MESS[$strMessPrefix.'CONTENT'] = 'Требования к содержимому';
	$MESS[$strMessPrefix.'RESTRICTIONS'] = 'Ограничения RSS-канала';
	$MESS[$strMessPrefix.'UPLOADING'] = 'Загрузка и обновление RSS-канала';
	$MESS[$strMessPrefix.'TROUBLESHOOTING'] = 'Часто встречающиеся ошибки';

$MESS[$strMessPrefix.'HOW_TO_EXPORT_STATIC_FILES'] = '
	<h2>Как выгружать статические файлы?</h2>
	<p>Для того, чтобы статические файлы попали в выгрузку, Вам необходимо внести в них определенные изменения (в частности - в верстку): контент, который предполагается выгружать в Яндекс.Турбо «оберните» html-комментарием TurboContent, чтобы получилось так:<br/>
		<pre>
&lt;!--TurboContent--&gt;
	&lt;p&gt;Строка 1&lt;/p&gt;
	&lt;p&gt;Строка 2&lt;/p&gt;
	&lt;p&gt;Строка 3&lt;/p&gt;
&lt;!--/TurboContent--&gt;
		</pre>
	</p>
	<p>Таких блоков может быть сколько угодно, содержимое объединяется в один блок.</p>
	<p>Заголовок берется прямо со страницы, из тега title.</p>
';

$MESS[$strMessPrefix.'ADD_FEED'] = 'Добавить файл-источник Турбо-страниц в Яндекс.Вебмастере';
$MESS[$strMessPrefix.'ADD_FEED_NOTICE'] = 'ссылка генерируется автоматически на основе домена и галочки SSL';
?>