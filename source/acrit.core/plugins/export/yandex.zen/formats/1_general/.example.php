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
        <title>���������</title>
        <link>http://example.com/</link>
        <description>
������ � ������� � ������. ������������ � ���, ��� ����������, �������� � ��������� ����.
</description>
        <language>ru</language>
        <item>
           <title>������� ����������� ����� � ������</title>
           <link>http://example.com/2023/07/04/android-happy-farmer</link>
           <pdalink>http://m.example.com/2023/07/04/android-happy-farmer</pdalink>
           <amplink>http://amp.example.com/2023/07/04/android-happy-farmer</amplink>
           <guid>2fd4e1c67a2d28fced849ee1bb76e7391b93eb12</guid>
           <pubDate>Tue, 4 Jul 2023 04:20:00 +0300</pubDate>
           <media:rating scheme="urn:simple">nonadult</media:rating>
           <author>���� ����������</author>
           <category>����������</category>
           <enclosure url="http://example.com/2023/07/04/pic1.jpg" type="image/jpeg"/>
           <enclosure url="http://example.com/2023/07/04/pic2.jpg" type="image/jpeg"/>
           <enclosure url="http://example.com/2023/07/04/video/420"
                      type="video/x-ms-asf"/>
           <description>
                <![CDATA[
����������� ����� ����� � ��������� ������������� ��������� �������� ��������
� ���������� ���������� ��������� ��������������.
]]>
            </description>
            <content:encoded>
                <![CDATA[

<p>����� ��������� ������ ����� ������.
���� ����� ����� ����������� ����������, ����� � ������ �����-��������� ��� ��,
��� � ������������ ������. ������ ����������� �������� ����.</p>
<figure>
    <img src="http://example.com/2023/07/04/pic1.jpg" width="1200" height="900">
        <figcaption>
������ �������-������ ������� �� ���� ������

            <span class="copyright">������ ���������</span>
        </figcaption>
    </figure>
    <p>����������� ������ ����� ����������� ��������. � ������ ��������������
� ���������� ����������� �����, ������� ���������� �������� �������-������.
������� � ��������� �� �������� ��� �����������. ������ ����������� ����.</p>
    <figure>
        <video width="1200" height="900">
            <source src="http://example.com/2023/07/04/video/42420" type="video/mp4">
            </video>
            <figcaption>
�������-������ ���������� ����� ��� ������ ������������ �����������

                <span class="copyright">������ ���������</span>
            </figcaption>
        </figure>
        <p>������ ������������ ����� �����. ������� ������ ��������.
����� �����������. ������ ���������� ��������.</p>
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