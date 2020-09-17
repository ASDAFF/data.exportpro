<?
$MESS['ACRIT_EXP_XML_SETTINGS_OFFERS_PREPROCESS'] = 'Торговые предложения внутри товаров';
	$MESS['ACRIT_EXP_XML_SETTINGS_OFFERS_PREPROCESS_HINT'] = 'Данная опция позволяет выгружать торговые предложения внутри каждого товара, а не отдельными элементами как обычно.<br/><br/>
	Например, если один товар с предложениями в XML выгружается как
	<pre>
	&lt;item&gt;&lt;/item&gt;<br/>
	&lt;item&gt;&lt;/item&gt;<br/>
	&lt;item&gt;&lt;/item&gt;<br/>
	</pre>
	то со включенной опцией выгрузка будет выглядеть примерно так:
	<pre>
	&lt;item&gt;<br/>
		&lt;offers&gt;<br/>
			&lt;item&gt;&lt;/item&gt;<br/>
			&lt;item&gt;&lt;/item&gt;<br/>
		&lt;/offers&gt;<br/>
	&lt;/item&gt;<br/>
	</pre>
	<b>Внимание!</b> Данный параметр применяется сразу ко всему профиля, а не к каждому инфоблоку в отдельности.';
$MESS['ACRIT_EXP_XML_SETTINGS_ADD_UMT'] = 'Добавлять UTM-метки';
	$MESS['ACRIT_EXP_XML_SETTINGS_ADD_UMT_HINT'] = 'Отметьте данную опцию, если нужно добавлять UTM-метки к ссылкам: при этом в списке полей добавляются новые поля (utm_content, utm_source и др).<br/><br/>Работает только в случае, если хотя бы для одного поля в настройках выбрана роль «URL».';
$MESS['ACRIT_EXP_XML_SETTINGS_ALL_CATEGORIES'] = 'Выгружать все активные категории';
	$MESS['ACRIT_EXP_XML_SETTINGS_ALL_CATEGORIES_HINT'] = 'Опция позволяет для выбранного инфоблока выгружать все категории, а не только те, в которых имеются товары для выгрузки.';
$MESS['ACRIT_EXP_XML_SETTINGS_DELETE_MODE'] = 'Удалять пустые теги';
	$MESS['ACRIT_EXP_XML_SETTINGS_DELETE_MODE_HINT'] = 'Данные опция позволяет удалить все пустые теги из выгрузки.';
	$MESS['ACRIT_EXP_XML_SETTINGS_DELETE_MODE_NO'] = 'ничего не делать';
	$MESS['ACRIT_EXP_XML_SETTINGS_DELETE_MODE_SIMPLE'] = 'удалить';
	$MESS['ACRIT_EXP_XML_SETTINGS_DELETE_MODE_ATTR'] = 'удалить (даже при наличии атрибутов)';

?>