<?
use \Bitrix\Main\Localization\Loc;
?>
<p><?=static::getMessage('GENERAL_PURPOSE');?></p>

<h2><?= static::getMessage('USEFUL_LINKS'); ?></h2>
<ul>
	<li>
		<a href="https://yandex.ru/support/partnermarket/offers.html#offers__list" target="_blank">
			<?=static::getMessage('DOCUMENTATION'); ?>
		</a>
	</li>
	<li>
		<a href="https://webmaster.yandex.ru/tools/xml-validator/" target="_blank">
			<?=static::getMessage('VALIDATOR'); ?>
		</a>
	</li>
</ul>

<p>
	<a href="" data-role="yandex-turbo-add-feed" target="_blank"	
		data-href="https://webmaster.yandex.ru/site/#SCHEME#:#DOMAIN#:#PORT#/turbo/sources/">
		<?=static::getMessage('ADD_FEED');?></a>
	<span style="color:gray; font-style:italic;">(<?=static::getMessage('ADD_FEED_NOTICE');?>)</span>
<p>
