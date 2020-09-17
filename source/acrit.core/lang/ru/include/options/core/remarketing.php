<?
$MESS['ACRIT_CORE_OPTION_GROUP_DYNAMIC_REMARKETING'] = 'Динамический ремаркетинг';
	$MESS['ACRIT_CORE_OPTION_GROUP_DYNAMIC_REMARKETING_HINT'] = '<b>Внимание!</b> Данные опции могут замедлить работу публичной части сайта, особенно на UTF-сайтах. Вместо включения ремаркетинга данным способом (автоматическим) рекомендуем добавить код ремаркетинга прямо вручную, в шаблоны соответствующих компонентов.<br/><br/>Опции данной группы позволяют автоматически добавить в публичную часть сайта необходимый для поддержки динамического ремаркетинга JavaScript-код.<br/><br/>Поддерживается макрос #ELEMENT_ID#, обозначающий ID элемента.<br/><br/>Если страница не содержит детального просмотра товара, скрипты добавлены не будут.';
	$MESS['ACRIT_CORE_OPTION_DYNAMIC_REMARKETING_GOOGLE'] = 'Google';
		$MESS['ACRIT_CORE_OPTION_DYNAMIC_REMARKETING_GOOGLE_HINT'] = 'Динамический ремаркетинг Google.<br/><br/>В поле необходимо скопировать JavaScript-код, например:<br/><code>&lt;script type=\'text/javascript\'&gt;<br/>var google_tag_params = {ecomm_pagetype:\'product\', ecomm_prodid:#ELEMENT_ID#};<br/>&lt;/script&gt;</code><br/>';
	$MESS['ACRIT_CORE_OPTION_DYNAMIC_REMARKETING_MAILRU'] = 'Mail.ru';
		$MESS['ACRIT_CORE_OPTION_DYNAMIC_REMARKETING_MAILRU_HINT'] = 'Динамический ремаркетинг mail.ru.<br/><br/>В поле необходимо скопировать JavaScript-код, например:<br/><code>&lt;script type=\'text/javascript\'&gt;<br/>var _tmr = _tmr || []; _tmr.push({type:\'itemView\', productid:#ELEMENT_ID#, pagetype:\'product\'});<br/>&lt;/script&gt;</code><br/>';
#
?>