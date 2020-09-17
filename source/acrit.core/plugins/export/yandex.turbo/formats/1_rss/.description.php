<?
use \Bitrix\Main\Localization\Loc;
?>
<p><?=static::getMessage('GENERAL_PURPOSE');?></p>

<p><?=static::getMessage('GENERAL_DESCRIPTION');?></p>

<br/>
<div>
	<?=static::getMessage('HOW_TO_EXPORT_STATIC_FILES');?>
</div>

<h2><?=static::getMessage('USEFUL_LINKS');?></h2>
<ul>
	<li>
		<a href="https://yandex.ru/support/webmaster/turbo/connection.html" target="_blank">
			<?=static::getMessage('ABOUT');?>
		</a>
	</li>
	<li>
		<a href="https://yandex.ru/dev/turbo/doc/quick-start/articles-docpage/" target="_blank">
			<?=static::getMessage('QUICK_START');?>
		</a>
	</li>
	<li>
		<a href="https://yandex.ru/dev/turbo/doc/rss/simple-rss-docpage/" target="_blank">
			<?=static::getMessage('EXAMPLE');?>
		</a>
	</li>
	<li>
		<a href="https://yandex.ru/dev/turbo/doc/rss/markup-docpage/" target="_blank">
			<?=static::getMessage('RSS_ELEMENTS');?>
		</a>
	</li>
	<li>
		<a href="https://yandex.ru/dev/turbo/doc/rss/requirements-docpage/" target="_blank">
			<?=static::getMessage('CONTENT');?>
		</a>
	</li>
	<li>
		<a href="https://yandex.ru/dev/turbo/doc/rss/quota-docpage/" target="_blank">
			<?=static::getMessage('RESTRICTIONS');?>
		</a>
	</li>
	<li>
		<a href="https://yandex.ru/dev/turbo/doc/rss/upload-and-update-docpage/" target="_blank">
			<?=static::getMessage('UPLOADING');?>
		</a>
	</li>
	<li>
		<a href="https://yandex.ru/dev/turbo/doc/rss/troubleshooting-docpage/" target="_blank">
			<?=static::getMessage('TROUBLESHOOTING');?>
		</a>
	</li>
</ul>

<p>
	<a href="" data-role="yandex-turbo-add-feed" target="_blank"	
		data-href="https://webmaster.yandex.ru/site/#SCHEME#:#DOMAIN#:#PORT#/turbo/sources/">
		<?=static::getMessage('ADD_FEED');?></a>
	<span style="color:gray; font-style:italic;">(<?=static::getMessage('ADD_FEED_NOTICE');?>)</span>
<p>