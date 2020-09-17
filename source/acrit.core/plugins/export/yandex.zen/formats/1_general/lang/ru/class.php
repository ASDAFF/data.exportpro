<?
$strMessPrefix = 'ACRIT_EXP_YANDEX_ZEN_GENERAL_';

// General
$MESS[$strMessPrefix.'NAME'] = 'Яндекс.Дзен';

// Default settings
$MESS[$strMessPrefix.'CHANNEL_TITLE'] = 'Название сайта';
$MESS[$strMessPrefix.'CHANNEL_TITLE_HINT'] = 'Название сайта. Указывается в настройках канала';
$MESS[$strMessPrefix.'CHANNEL_DESCRIPTION'] = 'Описание сайта';
$MESS[$strMessPrefix.'CHANNEL_DESCRIPTION_HINT'] = 'Описание сайта. Указывается в настройках канала';
$MESS[$strMessPrefix.'YANDEX_LANGUAGES'] = 'Язык ленты';
$MESS[$strMessPrefix.'YANDEX_LANGUAGES_HINT'] = 'Язык ленты RSS согласно стандарту ISO 639-1';


// Fields
$MESS[$strMessPrefix.'FIELD_TITLE_NAME'] = 'Заголовок публикации.';
$MESS[$strMessPrefix.'FIELD_TITLE_DESC'] = 'Заголовок публикации';
$MESS[$strMessPrefix.'FIELD_LINK_NAME'] = 'URL статьи';
$MESS[$strMessPrefix.'FIELD_LINK_DESC'] = 'URL статьи, данные которой транслируются в ленте RSS';
$MESS[$strMessPrefix.'FIELD_PDALINK_NAME'] = 'Ссылка на моб.версию';
$MESS[$strMessPrefix.'FIELD_PDALINK_DESC'] = 'Ссылка на адаптированную для мобильных устройств версию статьи';
$MESS[$strMessPrefix.'FIELD_AMPLINK_NAME'] = 'Ссылка на AMP-версию статьи';
$MESS[$strMessPrefix.'FIELD_AMPLINK_DESC'] = 'Ссылка на AMP-версию статьи';
$MESS[$strMessPrefix.'FIELD_PUBDATE_NAME'] = 'Дата и время публикации';
$MESS[$strMessPrefix.'FIELD_PUBDATE_DESC'] = 'Дата и время публикации';
$MESS[$strMessPrefix.'FIELD_AUTHOR_NAME'] = 'Имя автора публикации';
$MESS[$strMessPrefix.'FIELD_AUTHOR_DESC'] = 'Имя автора публикации';
$MESS[$strMessPrefix.'FIELD_CATEGORY_NAME'] = 'Тематика публикации';
$MESS[$strMessPrefix.'FIELD_CATEGORY_DESC'] = 'Тематика публикации. Публикация может относиться сразу к нескольким тематикам. Используемые категории:
<ul>
<li>Авто</li>
<li>Война</li>
<li>Дизайн</li>
<li>Дом</li>
<li>Еда</li>
<li>Здоровье</li>
<li>Знаменитости</li>
<li>Игры</li>
<li>Кино</li>
<li>Культура</li>
<li>Литература</li>
<li>Мода</li>
<li>Музыка</li>
<li>Наука</li>
<li>Общество</li>
<li>Политика</li>
<li>Природа</li>
<li>Происшествия</li>
<li>Психология</li>
<li>Путешествия</li>
<li>Спорт</li>
<li>Технологии</li>
<li>Фотографии</li>
<li>Хобби</li>
<li>Экономика</li>
<li>Юмор</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_ENCLOSURE_NAME'] = 'Описание изображений, аудио- и видеофайлов в публикации';
$MESS[$strMessPrefix.'FIELD_ENCLOSURE_DESC'] = 'Описание изображений, аудио- и видеофайлов в публикации. Может быть единственным упоминанием медиаконтента в публикации или дублировать элементы figure, media:content , размещаемые внутри content:encoded.
Каждому элементу figure или media:content должен соответствовать элемент enclosure в описании публикации. Например, если внутри публикации содержится два изображения и видеоролик, в описании источника должны быть размещены три элемента enclosure.
Если в публикации присутствует несколько вариантов одной иллюстрации, отличающихся только размером, элемент enclosure указывается один раз, и в нем прописывается URL изображения наибольшего размера';
$MESS[$strMessPrefix.'FIELD_RATING_NAME'] = 'Возрастной рейтинг';
$MESS[$strMessPrefix.'FIELD_RATING_DESC'] = 'Возрастной рейтинг. Строго ограниченные значения:
«adult» — контент, который можно показывать только взрослым;
«nonadult» — контент, который можно показывать взрослым и детям от 13 лет.';
$MESS[$strMessPrefix.'FIELD_CONTENT_NAME'] = 'Полный текст публикации';
$MESS[$strMessPrefix.'FIELD_CONTENT_DESC'] = 'Полный текст публикации (рекомендованный объем — не менее 300 знаков с пробелами) или видеоролик. Содержит внутри элементы для размещения медиаконтента.';
$MESS[$strMessPrefix.'FIELD_DESCRIPTION_NAME'] = 'Краткое содержание';
$MESS[$strMessPrefix.'FIELD_DESCRIPTION_DESC'] = 'Краткое содержание публикации';
$MESS[$strMessPrefix.'FIELD_IMAGE_NAME'] = 'Изображения из публикации';
$MESS[$strMessPrefix.'FIELD_IMAGE_DESC'] = 'Изображения, приведенные в публикации. Используются в элементе enclosure';
$MESS[$strMessPrefix.'FIELD_VIDEO_URL_NAME'] = 'Видео из публикации';
$MESS[$strMessPrefix.'FIELD_VIDEO_URL_DESC'] = 'Видео, приведенные в публикации. Используются в элементе enclosure';

# Steps

# Display results

#

?>
