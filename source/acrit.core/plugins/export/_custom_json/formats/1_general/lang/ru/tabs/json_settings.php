<?
$MESS['ACRIT_EXP_JSON_STRUCTURE'] = 'Структура JSON';
	$MESS['ACRIT_EXP_JSON_STRUCTURE_GENERAL'] = 'Общая структура JSON';
	$MESS['ACRIT_EXP_JSON_STRUCTURE_GENERAL_HINT'] = 'Здесь заполняется общая структура JSON';
	$MESS['ACRIT_EXP_JSON_STRUCTURE_NOTICE'] = 'Доступны следуюшие макросы:<br/>
#DATE# - текущая дата/время в формате сайта,<br/>
#DATE(формат даты/времени PHP)#<br/>
Например: #DATE(Y-m-d H:i:s)#, #DATE(c)#, #DATE(r)#<br/>
Имейте ввиду, что макросы не нужно заключать в кавычки - это при подстановке макроса будет сделано автоматически.';
	$MESS['ACRIT_EXP_JSON_STRUCTURE_EXAMPLE'] = '{
	"date": #DATE#,
	"items": [
		#JSON_ITEMS#
	]
}';
	$MESS['ACRIT_EXP_JSON_STRUCTURE_PLACEHOLDER'] = 'Например:'.PHP_EOL.htmlspecialcharsbx($MESS['ACRIT_EXP_JSON_STRUCTURE_EXAMPLE']);

$MESS['ACRIT_EXP_JSON_FIELDS'] = 'Поля JSON (каждое поле с новой строки)';
	$MESS['ACRIT_EXP_JSON_ELEMENT_FIELDS'] = 'Поля товаров в JSON-файле';
	$MESS['ACRIT_EXP_JSON_ELEMENT_FIELDS_HINT'] = 'Укажите здесь список полей (каждое поле в отдельной строке) для товаров.';
	$MESS['ACRIT_EXP_JSON_OFFER_FIELDS'] = 'Поля торговых предложений в JSON-файле';$MESS['ACRIT_EXP_JSON_ELEMENT_FIELDS_HINT'] = 'Укажите здесь список полей (каждое поле в отдельной строке) для товаров.';
	$MESS['ACRIT_EXP_JSON_OFFER_FIELDS_HINT'] = 'Укажите здесь список полей (каждое поле в отдельной строке) для торговых предложений. Если список полей для товаров и для ТП не различается, то поля для ТП можно не заполнять.';
	$MESS['ACRIT_EXP_JSON_OFFER_FIELDS_NOTICE'] = '<small>только если поля ТП отличаются от полей товаров</small>';
	$MESS['ACRIT_EXP_JSON_FIELD_PLACEHOLDER'] = 'Например:'.PHP_EOL.'#EXAMPLE#';
	$MESS['ACRIT_EXP_JSON_FIELDS_NOTICE'] = '<b>Внимание!</b> После изменения списка полей необходимо применить изменения. Если Вы меняете название поля, необходимо затем перенастроить соотв. поле (вкладка «Настройки инфоблоков»).';
	
$MESS['ACRIT_EXP_JSON_SETTINGS'] = 'Дополнительные настройки';
	$MESS['ACRIT_EXP_JSON_ADD_UTM'] = 'Добавлять UTM-метки';
		$MESS['ACRIT_EXP_JSON_ADD_UTM_HINT'] = 'Отметьте данную опцию, если нужно добавлять UTM-метки к ссылкам: при этом в списке полей добавляются новые поля (utm_content, utm_source и др).';
	$MESS['ACRIT_EXP_JSON_OFFERS_PREPROCESS'] = 'ТП внутри товаров';
	$MESS['ACRIT_EXP_JSON_UTM_FIELD'] = 'Поля, в которое нужно добавить UTM-метки';
		$MESS['ACRIT_EXP_JSON_UTM_FIELD_HINT'] = 'Укажите здесь поле (или если их несколько - через запятую), в которое необходимо добавить UTM-метки.';
	$MESS['ACRIT_EXP_JSON_OFFERS_PREPROCESS'] = 'ТП внутри товаров';
		$MESS['ACRIT_EXP_JSON_OFFERS_PREPROCESS_HINT'] = 'Данная опция позволяет выгружать ТП не в одном ряду с товаром, а внутри него, в отдельном поле (поле указывается ниже).';
	$MESS['ACRIT_EXP_JSON_OFFERS_PREPROCESS_FIELD'] = 'Поле товара для выгрузки ТП';
		$MESS['ACRIT_EXP_JSON_OFFERS_PREPROCESS_FIELD_HINT'] = 'Укажите, в каком поле товара будет выгружен массив с торговыми предложениями. Например, offers, или offers.items - в данном случае точка действует как разделение уровней вложенности - т.е. будет создано поле offers, внутри него поле items, и внутри него будет список ТП.';
	$MESS['ACRIT_EXP_JSON_TRANSFORM_FIELDS'] = 'Поля для трансформации';
		$MESS['ACRIT_EXP_JSON_TRANSFORM_FIELDS_HINT'] = 'Укажите здесь поля (через запятую) для трансформации, при которой вместо исходного массива, содержащего отдельно массивы каждой из характеристик, генерируется новый массив, содержащий элементы с разобранными характеристиками, например:
<table style="table-layout:fixed;width:100%;">
<tbody>
<tr>
<td style="vertical-align:top; width:50%;">
<pre>
{<br/>
	"sub": {<br/>
		"key": [<br/>
			"2222",<br/>
			"ffffffffff"<br/>
		],<br/>
		"value": [<br/>
			"22222222222",<br/>
			"gggggggggg"<br/>
		]<br/>
	}<br/>
}<br/>
</pre>
</td>
<td style="vertical-align:top; width:50%;">
<pre>
{<br/>
	"sub": [<br/>
		{<br/>
			"key": "2222",<br/>
			"value": "22222222222"<br/>
		},<br/>
		{<br/>
			"key": "ffffffffff",<br/>
			"value": "gggggggggg"<br/>
		}<br/>
	]<br/>
}<br/>
</pre>
</td>
</tr>
</tbody>
</table>
Это позволяет с помощью нескольких отдельных полей собирать массивы, группируя их между собой по порядку.<br/><br/>
Поля можно указывать любой вложенности - например, properties, или properties.delivery (поле delivery внутри поля properties)';
	$MESS['ACRIT_EXP_JSON_ENCODE_OPTIONS'] = 'Опции кодирования JSON';
		$MESS['ACRIT_EXP_JSON_ENCODE_OPTIONS_HINT'] = 'Укажите здесь опции кодирования JSON, например <b><code>JSON_PRETTY_PRINT</code></b>.';
		$MESS['ACRIT_EXP_JSON_ENCODE_OPTION_JSON_PRETTY_PRINT'] = 'Форматированный внешний вид';
		$MESS['ACRIT_EXP_JSON_ENCODE_OPTION_JSON_UNESCAPED_UNICODE'] = 'Не кодировать многобайтовые символы Unicode.';
		$MESS['ACRIT_EXP_JSON_ENCODE_OPTION_JSON_FORCE_OBJECT'] = 'Выдавать объекты также для неассоциативных массивов.';
		$MESS['ACRIT_EXP_JSON_ENCODE_OPTION_JSON_UNESCAPED_SLASHES'] = 'Не экранировать слеши /.';
		$MESS['ACRIT_EXP_JSON_ENCODE_OPTION_JSON_HEX_QUOT'] = 'Кодировать символы двойных кавычек.';
		$MESS['ACRIT_EXP_JSON_ENCODE_OPTION_JSON_HEX_APOS'] = 'Кодировать символы одинарных кавычек';
		$MESS['ACRIT_EXP_JSON_ENCODE_OPTION_JSON_HEX_TAG'] = 'Кодировать символы &lt; и &gt;.';
		$MESS['ACRIT_EXP_JSON_ENCODE_OPTION_JSON_HEX_AMP'] = 'Кодировать символы &amp;.';
		$MESS['ACRIT_EXP_JSON_ENCODE_OPTION_JSON_INVALID_UTF8_IGNORE'] = 'Игнорировать некорректные символы UTF-8';
		$MESS['ACRIT_EXP_JSON_ENCODE_OPTION_JSON_INVALID_UTF8_SUBSTITUTE'] = 'Кодировать некорректные символы UTF-8.';
		$MESS['ACRIT_EXP_JSON_ENCODE_OPTION_JSON_NUMERIC_CHECK'] = 'Кодированать строк, содержащих числа, как числа';
		$MESS['ACRIT_EXP_JSON_ENCODE_OPTION_JSON_PRESERVE_ZERO_FRACTION'] = 'Никогда не приводить дробные числа к целым.';
		$MESS['ACRIT_EXP_JSON_ENCODE_OPTION_JSON_UNESCAPED_LINE_TERMINATORS'] = 'Не кодировать символы конца строка.';
		$MESS['ACRIT_EXP_JSON_ENCODE_OPTION_JSON_PARTIAL_OUTPUT_ON_ERROR'] = 'Подставлять значения по умолчанию вместо ошибочных.';






?>