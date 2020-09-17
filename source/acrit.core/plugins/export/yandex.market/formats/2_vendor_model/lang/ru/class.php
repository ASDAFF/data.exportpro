<?
$strMessPrefix = 'ACRIT_EXP_YANDEX_MARKET_VENDOR_MODEL_';

// General
$MESS[$strMessPrefix.'NAME'] = 'Яндекс.Маркет (произвольный тип)';

// Fields
$MESS[$strMessPrefix.'FIELD_MODEL_NAME'] = 'Модель товара';
	$MESS[$strMessPrefix.'FIELD_MODEL_DESC'] = 'Модель товара.';
$MESS[$strMessPrefix.'FIELD_VENDOR_NAME'] = 'Название производителя';
	$MESS[$strMessPrefix.'FIELD_VENDOR_DESC'] = 'Название производителя данного товара.';
$MESS[$strMessPrefix.'FIELD_VENDOR_CODE_NAME'] = 'Код производителя';
	$MESS[$strMessPrefix.'FIELD_VENDOR_CODE_DESC'] = 'Код производителя для данного товара.';
$MESS[$strMessPrefix.'FIELD_TYPE_PREFIX_NAME'] = 'Тип / категория товара';
	$MESS[$strMessPrefix.'FIELD_TYPE_PREFIX_DESC'] = 'Тип / категория товара (например, «мобильный телефон», «стиральная машина», «угловой диван»).';
$MESS[$strMessPrefix.'FIELD_CONDITION_TYPE_NAME'] = 'Состояние товара (в случае уценки)';
	$MESS[$strMessPrefix.'FIELD_CONDITION_TYPE_DESC'] = 'Возможны только два варианта:
<ul>
	<li><code>likenew</code> — как новый (товар не был в употреблении);</li>
	<li><code>used</code> — подержанный (товар был в употреблении).</li>
</ul>
';
$MESS[$strMessPrefix.'FIELD_CONDITION_REASON_NAME'] = 'Состояние товара - причина уценки';
	$MESS[$strMessPrefix.'FIELD_CONDITION_REASON_DESC'] = 'Укажите причину уценки и подробно опишите недостатки. Длина текста не более 3000 символов (включая знаки препинания).<br/><br/>
Поле обязательно к заполнению в случае уценки товара.<br/><br/>
<b>Внимание!</b> Информация о причинах уценки должна быть исчерпывающей. Тексты не должны быть уклончивыми. Например, нельзя писать: «Причины уценки узнавайте у консультанта».
';

?>