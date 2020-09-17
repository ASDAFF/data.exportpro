<?
use \Bitrix\Main\Localization\Loc,
    \Acrit\Core\Helper;

$strExample = <<< XML
<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0"
    xmlns:content="http://purl.org/rss/1.0/modules/content/"
    xmlns:dc="http://purl.org/dc/elements/1.1/"
    xmlns:media="http://search.yahoo.com/mrss/"
    xmlns:atom="http://www.w3.org/2005/Atom"
    xmlns:georss="http://www.georss.org/georss">
    <channel>
        <title>Пастернак</title>
        <link>http://example.com/</link>
        <description>
Проект о фруктах и овощах. Рассказываем о том, как выращивать, готовить и правильно есть.
</description>
        <language>ru</language>
        <item>
           <title>Андроид восстановит ферму в Японии</title>
           <link>http://example.com/2023/07/04/android-happy-farmer</link>
           <pdalink>http://m.example.com/2023/07/04/android-happy-farmer</pdalink>
           <amplink>http://amp.example.com/2023/07/04/android-happy-farmer</amplink>
           <guid>2fd4e1c67a2d28fced849ee1bb76e7391b93eb12</guid>
           <pubDate>Tue, 4 Jul 2023 04:20:00 +0300</pubDate>
           <media:rating scheme="urn:simple">nonadult</media:rating>
           <author>Петр Стругацкий</author>
           <category>Технологии</category>
           <enclosure url="http://example.com/2023/07/04/pic1.jpg" type="image/jpeg"/>
           <enclosure url="http://example.com/2023/07/04/pic2.jpg" type="image/jpeg"/>
           <enclosure url="http://example.com/2023/07/04/video/420"
                      type="video/x-ms-asf"/>
           <description>
                <![CDATA[
Заброшенную землю рядом с токийским университетом Нисёгакуся передали андроиду
с внешностью известного японского хозяйственника.
]]>
            </description>
            <content:encoded>
                <![CDATA[

<p>Здесь находится полный текст статьи.
Этот текст может прерываться картинками, видео и другим медиа-контентом так же,
как в оригинальной статье. Пример вставленной картинки ниже.</p>
<figure>
    <img src="http://example.com/2023/07/04/pic1.jpg" width="1200" height="900">
        <figcaption>
Первый андроид-фермер смотрит на свои угодья

            <span class="copyright">Михаил Родченков</span>
        </figcaption>
    </figure>
    <p>Продолжение статьи после вставленной картинки. В статье рассказывается
о технологии вспахивании земли, которую использует японский андроид-фермер.
Поэтому в материале не обойтись без видеоролика. Пример видеоролика ниже.</p>
    <figure>
        <video width="1200" height="900">
            <source src="http://example.com/2023/07/04/video/42420" type="video/mp4">
            </video>
            <figcaption>
Андроид-фермер вспахивает землю при помощи собственного изобретения

                <span class="copyright">Михаил Родченков</span>
            </figcaption>
        </figure>
        <p>Статья продолжается после видео. Андроид копает картошку.
Фермы развиваются. Япония продолжает удивлять.</p>
]]>
            </content:encoded>
        </item>
    </channel>
</rss>
XML;
if(!Helper::isUtf()){
    $strExample = Helper::convertEncoding($strExample, 'UTF-8', 'CP1251');
}
?>
<div class="acrit-exp-plugin-example">
    <pre><code class="xml"><?=htmlspecialcharsbx($strExample);?></code></pre>
</div>
<script>
    $('.acrit-exp-plugin-example pre code.xml').each(function(i, block) {
        highlighElement(block);
    });
</script>