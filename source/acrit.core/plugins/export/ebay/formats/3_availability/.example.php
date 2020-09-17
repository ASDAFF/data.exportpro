<?
use
	\Acrit\Core\Helper;

$strExample = <<< XML
<?xml version="1.0"?>
<inventoryRequest>
	<inventory>
		<SKU>MIP_1103-1605-SKU1</SKU>
		<totalShipToHomeQuantity>25</totalShipToHomeQuantity>
	</inventory>
	<inventory>
		<SKU>MIP_1103-1605-SKU2</SKU>
		<totalShipToHomeQuantity>10</totalShipToHomeQuantity>
	</inventory>
</inventoryRequest>
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