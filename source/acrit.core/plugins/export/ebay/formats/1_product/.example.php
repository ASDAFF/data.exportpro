<?
use
	\Acrit\Core\Helper;

$strExample = <<< XML
<?xml version="1.0" encoding="UTF-8"?>
<productRequest>
	<!-- Single SKU-->
	<product>
		<SKU>MIP_SKU_08</SKU>
		<productInformation localizedFor="en_US">
			<title>Fashion Women Lady Elegant Crystal Pearl Ear Stud Earrings with SKU : MIP_SKU_09</title>
			<subtitle></subtitle>
			<description>
				<productDescription>Elegant Pearl Earring</productDescription>
				<additionalInfo></additionalInfo>
			</description>
			<attribute name="Brand">Fashion</attribute>
			<attribute name="Metal">Gold</attribute>
			<attribute name="Style">Stud</attribute>
			<UPC>716838224876</UPC>
			<ISBN></ISBN>
			<EAN></EAN>
			<MPN></MPN>
			<Brand></Brand>
			<ePID></ePID>
			<pictureURL>http://www.example.com/image123.jpg</pictureURL>
			<pictureURL>http://www.example.com/image123.jpg</pictureURL>
			<conditionInfo>
				<condition>New</condition>
				<conditionDescription></conditionDescription>
			</conditionInfo>
			<shippingDetails measurementSystem="ENGLISH">
				<weightMajor></weightMajor>
				<weightMinor></weightMinor>
				<length></length>
				<width></width>
				<height></height>
				<packageType></packageType>
			</shippingDetails>
		</productInformation>
	</product>
	    <!-- MSKU scenario -->
    <productVariationGroup>
        <groupID>MIP_GROUP_1103-1605</groupID>
        <groupInformation localizedFor="en_US">
            <variationVector>
                <name>Color</name>
                <name>Size (Women's)</name>
                <name>Size Type</name>
            </variationVector>
            <sharedProductInformation>
                <title>Title MIP_GROUP_1103-1605</title>
                <subtitle></subtitle>
                <description>
                    <productDescription>New Ralph Lauren Polo womens tops shirts! Black, Pink, Yellow, Blue.with group id MIP_GROUP_12</productDescription>
                    <additionalInfo></additionalInfo>
                </description>
                <attribute name="Brand">Ralph Lauren</attribute>
                <attribute name="Style">Polo Shirt</attribute>
                <pictureURL>http://www.example.com/image123.jpg</pictureURL>
                <shippingDetails measurementSystem="ENGLISH">
                    <weightMajor></weightMajor>
                    <weightMinor></weightMinor>
                    <length></length>
                    <width></width>
                    <height></height>
                    <packageType></packageType>
                </shippingDetails>
            </sharedProductInformation>
        </groupInformation>
    </productVariationGroup>
    <!-- First SKU for MSKU Group -->
    <product>
        <SKU variantOf="MIP_GROUP_1103-1605">MIP_1103-1605-SKU1</SKU>
        <productInformation localizedFor="en_US">
            <attribute name="Color">Pink</attribute>
            <attribute name="Size (Women's)">L</attribute>
            <attribute name="Size Type">Petites</attribute>
            <pictureURL>http://www.example.com/image123.jpg</pictureURL>
            <conditionInfo>
                <condition>New</condition>
                <conditionDescription></conditionDescription>
            </conditionInfo>
            <UPC>888000163472</UPC>
            <ISBN></ISBN>
            <EAN></EAN>
            <MPN></MPN>
            <Brand></Brand>
        </productInformation>
    </product>
    <!-- Second SKU for MSKU Group -->
    <product>
        <SKU variantOf="MIP_GROUP_1103-1605">MIP_1103-1605-SKU2</SKU>
        <productInformation localizedFor="en_US">
            <attribute name="Color">Blue</attribute>
            <attribute name="Size (Women's)">PL</attribute>
            <attribute name="Size Type">Small</attribute>
            <pictureURL>http://www.example.com/image123.jpg</pictureURL>
            <conditionInfo>
                <condition>New</condition>
                <conditionDescription></conditionDescription>
            </conditionInfo>
            <UPC>888000163471</UPC>
            <ISBN></ISBN>
            <EAN></EAN>
            <MPN></MPN>
            <Brand></Brand>
        </productInformation>
    </product>
</productRequest>
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