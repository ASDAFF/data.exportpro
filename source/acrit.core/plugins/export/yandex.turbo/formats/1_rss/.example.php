<?
use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper;

$strExample = <<< XML
<rss xmlns:yandex="http://news.yandex.ru" xmlns:media="http://search.yahoo.com/mrss/" xmlns:turbo="http://turbo.yandex.ru" version="2.0">
	<channel>
		<item turbo="true">
			<link>http://www.example.com/page1.html</link>
			<turbo:content>
				<![CDATA[
					<header>
						<figure>
							<img src="http://example.com/img.jpg" />
						</figure>
						<h1>Заголовок страницы</h1>
					</header>
					<h2>Заголовок страницы</h2>
					<p>Текст с <b>выделением</b> и списком:</p>
					<ul>
						<li>пункт 1;</li>
						<li>пункт 2.</li>
					</ul>
					<figure>
						<img src="http://example.com/img-for-video.jpg" />
						<figcaption>Подпись к картинке</figcaption>
					</figure>
					<iframe width="560" height="315" src="https://www.youtube.com/embed/<уникальный набор символов>" frameborder="0" allowfullscreen></iframe>
				]]>
			</turbo:content>
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
