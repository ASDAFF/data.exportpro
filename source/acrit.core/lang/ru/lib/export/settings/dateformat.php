<?
$strMessPrefix = 'ACRIT_EXP_SETTINGS_DATEFORMAT_';

$arFormats = array(
	\CDatabase::DateFormatToPHP(FORMAT_DATETIME),
	'Y-m-d H:i:s',
	'Y-m-d',
	'd.m.Y H:i',
	'd.m.Y',
);
$strFormats = '';
foreach($arFormats as $index => $strFormat){
	$strFormats .= '<tr><td style="padding-right:10px;white-space:nowrap;width:1px;"><b><code>'.$strFormat.'</code></b></td><td>'.date($strFormat).($index==0?' <b>(текущий формат)</b>':'').'</td></tr>';
}
#
$MESS[$strMessPrefix.'NAME'] = 'Изменить формат даты';
$MESS[$strMessPrefix.'DESC'] = 'Данная опция позволяет изменить формат даты/времени из одного формата в другой<br/><br/>Использутся <a href="http://php.net/manual/en/function.date.php" target="_blank">формат PHP</a>, например:<br/><table style="width:100%">'.$strFormats.'</table>';
$MESS[$strMessPrefix.'TEXT'] = ' => ';
$MESS[$strMessPrefix.'KEEP'] = 'Сохр.';
$MESS[$strMessPrefix.'KEEP_HINT'] = 'Отметьте данную галочку, если нужно сохранять значение, которое не было конвертировано по причине некорректного формата. В противном случае значение очищается.';
$MESS[$strMessPrefix.'CHANGE'] = 'Изменить дату/время:';
$MESS[$strMessPrefix.'CHANGE_DAYS'] = 'дней';
$MESS[$strMessPrefix.'CHANGE_HOURS'] = 'часов';
$MESS[$strMessPrefix.'CHANGE_MINUTES'] = 'минут';
$MESS[$strMessPrefix.'CHANGE_SECONDS'] = 'секунд';
$MESS[$strMessPrefix.'CHANGE_HINT'] = 'Вы можете увеличить (напр., +30) или уменьшить (напр., -20) значение даты.';
$MESS[$strMessPrefix.'CHANGE_LOG_MESSAGE'] = 'Ошибка изменения даты: #MESSAGE# [поле: #FIELD#, значение: #VALUE#].';
?>