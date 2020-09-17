<?
namespace Acrit\Core\Export;
?>

<div class="acrit-exp-plugin-example">
	<?
	foreach($arFormats as $strFormat => $strExampleData){
		$strID = 'x'.uniqid().time();
		?><div data-format="<?=$strFormat;?>" data-id="<?=$strID;?>"><?
		switch($strFormat){
			case 'XML':
				?><pre><code class="xml" id="<?=$strID;?>"><?=htmlspecialcharsbx($strExampleData);?></code></pre><?
				?><script>highlighElement(document.getElementById('<?=$strID;?>'));</script><?
				break;
			case 'CSV':
				?><pre><code class="csv" id="<?=$strID;?>"><?=htmlspecialcharsbx($strExampleData);?></code></pre><?
				break;
			case 'JSON':
				?><pre><code class="json" id="<?=$strID;?>"><?=htmlspecialcharsbx($strExampleData);?></code></pre><?
				?><script>highlighElement(document.getElementById('<?=$strID;?>'));</script><?
				break;
		}
		?></div><?
	}
	unset($strFormat, $strExampleData, $strID);
	?>
</div>
<script>
if(!window.acritExpUniversalPluginsExampleComplete){
	$(document).delegate('#acrit_exp_plugin_settings_export_format', 'change', function(e){
		var format = $(this).val();
		var shown = $('.acrit-exp-plugin-example [data-format]').hide()
			.filter('[data-format='+format+']').show().length > 0;
		$('#tr_PLUGIN_EXAMPLE_HEADING').css('display', shown ? 'table-row' : 'none');
	});
	window.acritExpUniversalPluginsExampleComplete = true;
}
$('#acrit_exp_plugin_settings_export_format').trigger('change');
</script>