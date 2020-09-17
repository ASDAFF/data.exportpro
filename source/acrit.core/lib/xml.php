<?
/**
 *	Class to work with XML, not requires simplexml
 */

namespace Acrit\Core;

use
	\Acrit\Core\Helper;

Helper::loadMessages(__FILE__);

class Xml {
	
	/**
	 *	Transform array to XML (example see lower)
	 */
	public static function arrayToXml($arXml, $intDepthLevel=1, $bFirstLevelIsDifferent=true){
		$strResult = '';
		$strIndent = static::writeIndent($intDepthLevel);
		if($bFirstLevelIsDifferent && $intDepthLevel === 1) {
			reset($arXml);
			$strKey = key($arXml);
			$arXml = array(
				$strKey => array(
					$arXml[$strKey],
				),
			);
		}
		foreach($arXml as $strTagName => $arXmlItems) {
			if(is_array($arXmlItems)) {
				foreach($arXmlItems as $arXmlItem){
					if(is_array($arXmlItem)) {
						$arAttributes = $arXmlItem['@'];
						$arContent = $arXmlItem['#'];
					}
					else {
						$arAttributes = array();
						$arContent = $arXmlItem;
					}
					$strAttributes = static::writeAttributes($arAttributes);
					# If tag contains children tags
					if(is_array($arContent)){
						$strResult .= $strIndent.'<'.$strTagName.$strAttributes.'>'.PHP_EOL;
						$strResult .= static::arrayToXml($arContent, $intDepthLevel+1);
						$strResult .= $strIndent.'</'.$strTagName.'>'.PHP_EOL;
					}
					# Empty data: <x />
					elseif(Helper::isEmpty($arContent)){
						$strResult .= $strIndent.'<'.$strTagName.$strAttributes.'/>'.PHP_EOL;
					}
					# If tag contains text
					else{
						$strResult .= $strIndent.'<'.$strTagName.$strAttributes.'>'.$arContent.'</'.$strTagName.'>'.PHP_EOL;
					}
				}
			}
		}
		static::removeUnicodeSymbols($strResult);
		return $strResult;
	}
	
	/**
	 *	Write multiple indents
	 */
	protected static function writeIndent($intDepthLevel){
		$strResult = '';
		for($i = 1; $i <= $intDepthLevel - 1; $i++) {
			$strResult .= "\t";
		}
		return $strResult;
	}
	
	/**
	 *	Write tag attributes
	 */
	protected static function writeAttributes($arAttributes){
		$arResult = array();
		if(is_array($arAttributes)){
			foreach($arAttributes as $strAttributeName => $strAttributeValue) {
				$mReplaceChars = array("\r", "\n", "\t");
				$strAttributeName = str_replace($mReplaceChars, '', htmlspecialcharsbx($strAttributeName));
				$strAttributeValue = str_replace($mReplaceChars, '', htmlspecialcharsbx($strAttributeValue));
				$arResult[] = sprintf('%s="%s"', $strAttributeName, $strAttributeValue);
			}
		}
		return !empty($arResult) ? ' '.implode(' ', $arResult) : '';
	}
	
	/**
	 *
	 */
	public static function includeBitrixLib(){
		require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/classes/general/xml.php');
	}
	
	/**
	 *	XML to array (using Bitrix library)
	 */
	public static function xmlToArray($strXml, $bAutoConvert=false){
		static::includeBitrixLib();
		$obXml = new \CDataXML();
		$obXml->delete_ns = false;
		$obXml->LoadString($strXml);
		$arResult = $obXml->getArray();
		if($bAutoConvert){
			$bUtf = true;
			if(is_object($obXml->tree) && strlen($obXml->tree->encoding) && toUpper($obXml->tree->encoding) != 'UTF-8'){
				$bUtf = false;
			}
			if(Helper::isUtf() && !$bUtf){
				$arResult = Helper::convertEncoding($arResult, 'CP1251', 'UTF-8');
			}
			elseif(!Helper::isUtf() && $bUtf){
				$arResult = Helper::convertEncoding($arResult, 'UTF-8', 'CP1251');
			}
		}
		unset($obXml);
		return $arResult;
	}
	
	/**
	 *	Add offset to XML
	 */
	public static function addOffset($strXML, $intAmount=1){
		$strOffset = str_repeat("\t", $intAmount);
		return $strOffset.str_replace("\n", "\n".$strOffset, $strXML);
		/*
		replace with this:
		return preg_replace('#^(.*?)$#m', $strOffset.'$1', strXMLX);
		*/
	}
	
	/**
	 *	Helper for add tag
	 *	Simple mode <tag>value</tag>
	 */
	public static function addTag($mValue){
		$arResult = array();
		if(is_array($mValue)){
			foreach($mValue as $strValueItem){
				$arResult[] = array(
					'#' => $strValueItem,
				);
			}
		}
		else {
			$arResult['#'] = $mValue;
		}
		return $arResult;
	}
	
	/**
	 *	Helper for add tag with multiple subtags
	 *	Multiple mode:
	 *	<tag>
	 *		<sub>value 1</sub>
	 *		<sub>value 2</sub>
	 *		<sub>value 3</sub>
	 *	</tag>
	 */
	public static function addTagWithSubtags($mValue, $strSubtagName, $mCallback=false, $arCallbackParams=array()){
		$bCallable = $mCallback && is_callable($mCallback) ? true : false;
		if(!$bCallable && !$mValue){
			$mValue = array($mValue);
		}
		$arResult = array(
			array(
				'#' => array(
					$strSubtagName => $bCallable
						? call_user_func($mCallback, $mValue, $arCallbackParams)
						: static::addTag($mValue),
				),
			),
		);
		return $arResult;
	}
	
	/**
	 *	Remove unsupporter unicode symbols from XML
	 */
	public static function removeUnicodeSymbols(&$strText){
		$arTrashSymbols = array('');
		$strText = str_replace($arTrashSymbols, '', $strText);
	}

}

/*
EXAMPLE
=======
$arXml = array(
	'offer' => array(
		'@' => array('id'=>'123','date'=>'today'),
		'#' => array(
			'url' => array(
				array(
					'@' => array('absolute'=>'true'),
					'#' => 'http://site.ru',
				),
			),
			'param' => array(
				array(
					'@' => array('name'=>'param1'),
					'#' => 'value1',
				),
				array(
					'@' => array('name'=>'param2'),
					'#' => 'value2',
				),
				array(
					'@' => array('name'=>'param3'),
					'#' => 'value3',
				),
			),
			'custom' => array(
				array(
					'@' => array('name'=>'param1'),
					'#' => 'value1',
				),
				array(
					'@' => array('name'=>'param2'),
					'#' => array(
						'subparam' => array(
							array(
								'#' => 'Content here..',
							),
							array(
								'@' => array('text'=>'OK'),
								'#' => array(
									'finish' => array(
										array(
											'@' => array(),
											'#' => 'Done!',
										),
									),
								),
							),
						),
					),
				),
				array(
					'@' => array('name'=>'param3'),
					'#' => 'value3',
				),
			),
		),
	),
);
$strXml = htmlspecialchars(\Acrit\Core\Xml::arrayToXml($arXml));

RESULT IS:
<offer id="123" date="today">
	<url absolute="true">http://site.ru</url>
	<param name="param1">value1</param>
	<param name="param2">value2</param>
	<param name="param3">value3</param>
	<custom name="param1">value1</custom>
	<custom name="param2">
		<subparam>Content here..</subparam>
		<subparam text="OK">
			<finish>Done!</finish>
		</subparam>
	</custom>
	<custom name="param3">value3</custom>
</offer>
*/
?>