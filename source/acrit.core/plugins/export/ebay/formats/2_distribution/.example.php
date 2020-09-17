<?
use
	\Acrit\Core\Helper;

$strExample = <<< XML
<?xml version="1.0" encoding="UTF-8"?>
<distributionRequest>
  <!-- First SKU -->
	<distribution>
		<SKU>MIP_SKU_14</SKU>
		<channelDetails>
			<channelID>EBAY_US</channelID>
			<category>53159</category>
			<shippingPolicyName>shipping</shippingPolicyName> <!-- Replace business policy names with your seller's business policy names. -->
			<shippingCostOverrides>
				<shippingCost></shippingCost>
				<additionalCost></additionalCost>
				<surcharge></surcharge>
				<priority></priority>
				<shippingServiceType></shippingServiceType>
			</shippingCostOverrides>
			<paymentPolicyName>payment</paymentPolicyName>
			<returnPolicyName>return</returnPolicyName>
			<maxQuantityPerBuyer></maxQuantityPerBuyer>
			<pricingDetails>
				<listPrice>24.00</listPrice>
				<strikeThroughPrice></strikeThroughPrice>
				<minimumAdvertisedPrice></minimumAdvertisedPrice>
				<minimumAdvertisedPriceHandling></minimumAdvertisedPriceHandling>
				<soldOffEbay></soldOffEbay>
				<soldOnEbay></soldOnEbay>
			</pricingDetails>
			<storeCategory1Name></storeCategory1Name>
			<storeCategory2Name></storeCategory2Name>
			<templateName>description.html</templateName>
			<customFields>
				<customField>
					<name>Features</name>
					<value>Ceramic</value>
				</customField>
				<customField>
					<name>Shipping</name>
					<value>Free</value>
				</customField>
			</customFields>
			<applyTax></applyTax>
			<taxCategory></taxCategory>
			<eBayNowEligible>true</eBayNowEligible>
			<pickupInStoreEligible>false</pickupInStoreEligible>
			<VATPercent></VATPercent>
			<eligibleForEbayPlus></eligibleForEbayPlus>
			<lotSize></lotSize>
		</channelDetails>
	</distribution>
	<!-- 2nd SKU -->
	<distribution>
		<SKU>MIP_SKU_15</SKU>
		<channelDetails>
			<channelID>EBAY_US</channelID>
			<category>53159</category>
			<shippingPolicyName>shipping</shippingPolicyName> <!-- Replace business policy names with your seller's business policy names. -->
			<shippingCostOverrides>
				<shippingCost></shippingCost>
				<additionalCost></additionalCost>
				<surcharge></surcharge>
				<priority></priority>
				<shippingServiceType></shippingServiceType>
			</shippingCostOverrides>
			<paymentPolicyName>payment</paymentPolicyName>
			<returnPolicyName>return</returnPolicyName>
			<maxQuantityPerBuyer></maxQuantityPerBuyer>
			<pricingDetails>
				<listPrice>24.00</listPrice>
				<strikeThroughPrice></strikeThroughPrice>
			    <minimumAdvertisedPrice></minimumAdvertisedPrice>
			    <minimumAdvertisedPriceHandling></minimumAdvertisedPriceHandling> <!-- Possible Values are: PRE_CHECKOUT and DURING_CHECKOUT -->
			    <soldOffEbay></soldOffEbay>
			    <soldOnEbay></soldOnEbay>
			</pricingDetails>
			<storeCategory1Name></storeCategory1Name>
			<storeCategory2Name></storeCategory2Name>
			<templateName>description.html</templateName>
			<customFields>
				<customField>
					<name>Features</name>
					<value>Ceramic</value>
				</customField>
				<customField>
					<name>Shipping</name>
					<value>Free</value>
				</customField>
			</customFields>
			<applyTax></applyTax>
			<eBayNowEligible></eBayNowEligible>
			<VATPercent></VATPercent>
			<eligibleForEbayPlus></eligibleForEbayPlus>
		</channelDetails>
	</distribution>
</distributionRequest>
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