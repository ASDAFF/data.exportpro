<?
$MESS["DATA_EXPORTPRO_NE_VYBRANO"] = "не выбрано--";
$MESS["DATA_EXPORTPRO_CONVERT_FIELDSET_FIELD"] = "Свойство или поле";
$MESS["DATA_EXPORTPRO_CONVERT_FIELDSET_CONST"] = "Постоянное значение";
$MESS["DATA_EXPORTPRO_CONVERT_FIELDSET_CONDITION_TRUE"] = "Условие выполнено";
$MESS["DATA_EXPORTPRO_CONVERT_FIELDSET_CONDITION_FALSE"] = "Условие не выполнено";
$MESS["DATA_EXPORTPRO_CONVERT_FIELDSET_CONDITION_ADD"] = "Добавить";
$MESS["DATA_EXPORTPRO_CONVERT_FIELDSET_REQUIRED"] = "Обязательное";
$MESS["DATA_EXPORTPRO_CONVERT_FIELDSET_CONDITION"] = "Условие";
$MESS["DATA_EXPORTPRO_CONVERT_FIELDSET_HEADER"] = "Установка соответствия конвертации полей";
$MESS["DATA_EXPORTPRO_CONVERT_FIELDSET_DESCRIPTION"] = "
    <b>\"КОНВЕРТАЦИЯ ЗНАЧЕНИЙ\"</b> позволяет заменить набор значений в формируемом файле выгрузки.<br><br>
    В левой колонке формируется набор данных для замены. В правой - набор, на который будет произведена замена.<br><br>
    Например, в случае указания в левой колонке значения \"футбол\", а в правой значения \"хоккей\" при генерации файла выгрузки во всех данных, полученных из полей экспорта, будет произведена прямая замена слова \"футбол\" на слово \"хоккей\"
";
$MESS["DATA_EXPORTPRO_CONVERT_FIELDSET_DELETE_ONEMPTY"] = "Удалять тэг, если значение не установлено";
$MESS["DATA_EXPORTPRO_CONVERT_FIELDSET_DELETE_ONEMPTY_ATTRIBUTES"] = "Удалять пустые атрибуты в теге";
$MESS["DATA_EXPORTPRO_CONVERT_FIELDSET_URL_ENCODE"] = "URL-кодирование строки";
$MESS["DATA_EXPORTPRO_CONVERT_FIELDSET_CONVERT_CASE"] = "Изменить регистр";
$MESS["DATA_EXPORTPRO_CONVERT_FIELDSET_HTML_ENCODE"] = "Экранировать спецсимволы";
$MESS["DATA_EXPORTPRO_CONVERT_FIELDSET_HTML_TO_TXT"] = "Перевести HTML в TXT";
$MESS["DATA_EXPORTPRO_CONVERT_FIELDSET_SKIP_UNTERM_ELEMENT"] = "Пропустить некорректное предложение";
$MESS["DATA_EXPORTPRO_CONVERT_FIELDSET_TEXT_LIMIT"] = "Количество символов в обрезаной строке";
$MESS["DATA_EXPORTPRO_CONVERT_FIELDSET_MULTIPROP_LIMIT"] = "Количество выбираемых значений из множественного поля";


$MESS["DATA_EXPORTPRO_CONVERT_FIELDSET_REQUIRED_HELP"] = "Установка параметра означает добавление проверки на обязательность заполнения данного тега и в случае, если в элементе инфоблока будут отсутствовать значения, все элементы у которых будет отсутствовать значение, попадут в лог ошибок";
$MESS["DATA_EXPORTPRO_CONVERT_FIELDSET_CONDITION_HELP"] = "Включение условий формирования тега. Позоляет формировать нужные значения в файле экспорта в зависимости от исходных данных. Например, если у вас в свойстве элемента инфоблока присутствует значение \"студия\", а Яндекс.Недвижимость принимает только значения в цифрах, то вы, используя условия, можете переустановить значения в конечном файле, добавив условие, что если в тег попадает значение \"студия\", его надо устанавливать равным \"1\". И так же можно создавать группы условий для более сложных вариантов настройки";
$MESS["DATA_EXPORTPRO_CONVERT_FIELDSET_DELETE_ONEMPTY_HELP"] = "Установка этой опции позволит полнотью удалить даже сам тег из файла в случае, если ему устанавливается пустое значение";
$MESS["DATA_EXPORTPRO_CONVERT_FIELDSET_DELETE_ONEMPTY_ATTRIBUTES_HELP"] = "Установка этой опции позволит полнотью удалить атрибуты тега из файла в случае, если им устанавливается пустое значение";
$MESS["DATA_EXPORTPRO_CONVERT_FIELDSET_URL_ENCODE_HELP"] = "Установка этой опции включает механизм кодирования при передаче значений свойств элементов инфоблока для замены символов, которые используются как служебные.<br/><br/> Тут полный список кодирования:<br/> <a href=\"http://web-developer.name/urlcode/\" target=\"_blank\">http://web-developer.name/urlcode/</a>";
$MESS["DATA_EXPORTPRO_CONVERT_FIELDSET_CONVERT_CASE_HELP"] = "Установка данного флага позволяет привести все значения к нижнему регистру";
$MESS["DATA_EXPORTPRO_CONVERT_FIELDSET_HTML_ENCODE_HELP"] = "Установка данного флага позволяет экранировать все спецсимволы, передающиеся в значениях свойств элементов инфоблоков";
$MESS["DATA_EXPORTPRO_CONVERT_FIELDSET_HTML_TO_TXT_HELP"] = "Установка данного флага позволяет перевести все значениях свойств элементов инфоблоков из вида HTML к текстововму, вырезав все HTML теги из исходного кода";
$MESS["DATA_EXPORTPRO_CONVERT_FIELDSET_SKIP_UNTERM_ELEMENT_HELP"] = "Пропустить некорректное предложение";
$MESS["DATA_EXPORTPRO_CONVERT_FIELDSET_TEXT_LIMIT_HELP"] = "Установка количетсва символов, до которого будет обрезана строка в файле выгрузки";
$MESS["DATA_EXPORTPRO_CONVERT_FIELDSET_MULTIPROP_LIMIT_HELP"] = "Количество выбираемых значений из множественного поля";
?>