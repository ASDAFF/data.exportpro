<?
$MESS["KIT_EXPORTPRO_NE_VYBRANO"] = "не выбрано--";
$MESS["KIT_EXPORTPRO_FIELDSET_FIELD"] = "Свойство или поле";
$MESS["KIT_EXPORTPRO_FIELDSET_CONST"] = "Постоянное значение";
$MESS["KIT_EXPORTPRO_FIELDSET_COMPLEX"] = "Комплексное значение";
$MESS["KIT_EXPORTPRO_FIELDSET_COMPOSITE"] = "Композитное значение";
$MESS["KIT_EXPORTPRO_FIELDSET_CONDITION_TRUE"] = "Условие выполнено";
$MESS["KIT_EXPORTPRO_FIELDSET_CONDITION_FALSE"] = "Условие не выполнено";
$MESS["KIT_EXPORTPRO_FIELDSET_CONDITION_ADD"] = "Добавить";
$MESS["KIT_EXPORTPRO_FIELDSET_ADD_PART_TO_COMPOSITE_FIELD"] = "Добавить поле в композитное значение";
$MESS["KIT_EXPORTPRO_FIELDSET_REQUIRED"] = "Обязательное";
$MESS["KIT_EXPORTPRO_FIELDSET_CONDITION"] = "Условие";
$MESS["KIT_EXPORTPRO_FIELDSET_HEADER"] = "Настройка полей экспорта";
$MESS["KIT_EXPORTPRO_FIELDSET_DESCRIPTION"] = "
    <b>\"ПОЛЯ-ЭКСПОРТА\"</b> заменяются значениями. Если значение не задано или неопределено,<br>
    то тэг в выгрузке предложения будет пропущен. Если установлена галочка <b>\"обязательное\"</b> поле и тэг<br>
    не определен или пропущен, то товарное предложение будет исключено из выгрузки.<br>
    Если установлена галочка <b>\"условие\"</b>, то для каждого этого значения товарного предложения оно будет проверяться<br>.
    Если галочка <b>\"условие\"</b> установлена и блока условий нет, снимите галочку и установите снова.<br>
";

$MESS["KIT_EXPORTPRO_FIELDSET_COMPOSITE_DIVIDER"] = "Разделитель";

$MESS["KIT_EXPORTPRO_FIELDSET_DELETE_ONEMPTY"] = "Удалять тэг, если значение не установлено";
$MESS["KIT_EXPORTPRO_FIELDSET_DELETE_ONEMPTY_ATTRIBUTES"] = "Удалять пустые атрибуты в теге";
$MESS["KIT_EXPORTPRO_FIELDSET_URL_ENCODE"] = "URL-кодирование строки";
$MESS["KIT_EXPORTPRO_FIELDSET_CONVERT_CASE"] = "Изменить регистр";
$MESS["KIT_EXPORTPRO_FIELDSET_HTML_ENCODE"] = "Экранировать спецсимволы";
$MESS["KIT_EXPORTPRO_FIELDSET_HTML_ENCODE_CUT"] = "Вырезать спецсимволы";
$MESS["KIT_EXPORTPRO_FIELDSET_HTML_TO_TXT"] = "Перевести HTML в TXT";
$MESS["KIT_EXPORTPRO_FIELDSET_SKIP_UNTERM_ELEMENT"] = "Пропустить некорректное предложение";
$MESS["KIT_EXPORTPRO_FIELDSET_TEXT_LIMIT"] = "Количество символов в обрезаной строке";
$MESS["KIT_EXPORTPRO_FIELDSET_MULTIPROP_LIMIT"] = "Количество выбираемых значений из множественного поля";

$MESS["KIT_EXPORTPRO_FIELDSET_CONVERT_CONDITION_ADD"] = "Добавить";
$MESS["KIT_EXPORTPRO_FIELDSET_CONVERT_DATA"] = "Конвертация данных";

$MESS["KIT_EXPORTPRO_FIELDSET_ROUND_PRECISION"] = "Количество десятичных знаков";
$MESS["KIT_EXPORTPRO_FIELDSET_ROUND_MODE_UP"] = "Округлять в большую сторону";
$MESS["KIT_EXPORTPRO_FIELDSET_ROUND_MODE_DOWN"] = "Округлять в меньшую сторону";
$MESS["KIT_EXPORTPRO_FIELDSET_ROUND_MODE_EVEN"] = "Округлять в сторону ближайшего четного знака";
$MESS["KIT_EXPORTPRO_FIELDSET_ROUND_MODE_ODD"] = "Округлять в сторону ближайшего нечетного знака";
$MESS["KIT_EXPORTPRO_FIELDSET_MINIMUM_OFFER_PRICE"] ="Получить минимальную цену торговых предложений";
$MESS["KIT_EXPORTPRO_FIELDSET_MINIMUM_OFFER_PRICE_CODE"] ="Название поля экспорта минимальной цены";

$MESS["KIT_EXPORTPRO_FIELDSET_REQUIRED_HELP"] = "Установка параметра означает добавление проверки на обязательность заполнения данного тега и в случае, если в элементе инфоблока будут отсутствовать значения, все элементы у которых будет отсутствовать значение, попадут в лог ошибок";
$MESS["KIT_EXPORTPRO_FIELDSET_CONDITION_HELP"] = "Включение условий формирования тега. Позоляет формировать нужные значения в файле экспорта в зависимости от исходных данных. Например, если у вас в свойстве элемента инфоблока присутствует значение \"студия\", а Яндекс.Недвижимость принимает только значения в цифрах, то вы, используя условия, можете переустановить значения в конечном файле, добавив условие, что если в тег попадает значение \"студия\", его надо устанавливать равным \"1\". И так же можно создавать группы условий для более сложных вариантов настройки";
$MESS["KIT_EXPORTPRO_FIELDSET_DELETE_ONEMPTY_HELP"] = "Установка этой опции позволит полнотью удалить даже сам тег из файла в случае, если ему устанавливается пустое значение";
$MESS["KIT_EXPORTPRO_FIELDSET_DELETE_ONEMPTY_ATTRIBUTES_HELP"] = "Установка этой опции позволит полнотью удалить атрибуты тега из файла в случае, если им устанавливается пустое значение";
$MESS["KIT_EXPORTPRO_FIELDSET_URL_ENCODE_HELP"] = "Установка этой опции включает механизм кодирования при передаче значений свойств элементов инфоблока для замены символов, которые используются как служебные.<br/><br/> Тут полный список кодирования:<br/> <a href=\"http://web-developer.name/urlcode/\" target=\"_blank\">http://web-developer.name/urlcode/</a>";
$MESS["KIT_EXPORTPRO_FIELDSET_CONVERT_CASE_HELP"] = "Установка данного флага позволяет привести все значения к нижнему регистру";
$MESS["KIT_EXPORTPRO_FIELDSET_HTML_ENCODE_HELP"] = "Установка данного флага позволяет экранировать все спецсимволы, передающиеся в значениях свойств элементов инфоблоков";
$MESS["KIT_EXPORTPRO_FIELDSET_HTML_ENCODE_CUT_HELP"] = "Установка данного флага позволяет вырезать все спецсимволы, передающиеся в значениях свойств элементов инфоблоков";
$MESS["KIT_EXPORTPRO_FIELDSET_HTML_TO_TXT_HELP"] = "Установка данного флага позволяет перевести все значениях свойств элементов инфоблоков из вида HTML к текстововму, вырезав все HTML теги из исходного кода";
$MESS["KIT_EXPORTPRO_FIELDSET_SKIP_UNTERM_ELEMENT_HELP"] = "Пропустить некорректное предложение";
$MESS["KIT_EXPORTPRO_FIELDSET_TEXT_LIMIT_HELP"] = "Установка количетсва символов, до которого будет обрезана строка в файле выгрузки";
$MESS["KIT_EXPORTPRO_FIELDSET_MULTIPROP_LIMIT_HELP"] = "Количество выбираемых значений из множественного поля";
$MESS["KIT_EXPORTPRO_FIELDSET_CONVERT_DATA_HELP"] = "Установка соответствия конвертируемых значений в поле";
$MESS["KIT_EXPORTPRO_FIELDSET_ROUND_PRECISION_HELP"] = "Количество десятичных знаков дробной части";
$MESS["KIT_EXPORTPRO_FIELDSET_MINIMUM_OFFER_PRICE_HELP"] = "Функция получения минимальной цены <br>выбранного типа среди торговых предложений товара";
$MESS["KIT_EXPORTPRO_FIELDSET_MINIMUM_OFFER_PRICE_CODE_HELP"] ="Название <b>#ПОЛЯ ЭКСПОРТА#</b><br>которому будет присвоена<br>минимальная цена торговых предложений";
$MESS["KIT_EXPORTPRO_FIELDSET_ROUND_PRECISION_HELP"] = "Количество десятичных знаков дробной части";
$MESS["KIT_EXPORTPRO_FIELDSET_COMPOSITE_DIVIDER_HELP"] = "Укажите разделитель между полями композитного значения";
?>