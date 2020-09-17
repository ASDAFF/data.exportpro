<?
/**
 *	Class for teacher
 */

namespace Acrit\Core;

use
	\Acrit\Core\Helper;

Helper::loadMessages(__FILE__);

class Teacher {
	
	const FUNC_BEGIN = '[!#[';
	const FUNC_END = ']#]';
	const FUNC_QUOTE = '"';
	
	/**
	 *	
	 */
	public static function getTeacherJs(&$arTeacher){
		
		# Prepare
		$arTeacherJson = [
			'debug' => $arTeacher['DEBUG'] === true,
			'code' => $arTeacher['CODE'],
			'steps' => [],
			'labels' => [],
		];
		
		# Title
		$arTeacherJson['labels']['title'] = isset($arTeacher['TITLE']) 
			? $arTeacher['TITLE']
			: static::getMessage('ACRIT_EXP_TEACHER_'.$key);
		
		# Splash screen
		if(is_array($arTeacher['SPLASH_SCREEN'])){
			$arTeacherJson['splashScreen'] = [];
			$arTeacherJson['splashScreen']['description'] = $arTeacher['SPLASH_SCREEN']['DESCRIPTION'];
			$arTeacherJson['splashScreen']['html'] = $arTeacher['SPLASH_SCREEN']['HTML'];
			$arTeacherJson['splashScreen']['cssStyles'] = $arTeacher['SPLASH_SCREEN']['CSS'];
		}
		
		# Description toggle text
		if(!strlen($arTeacher['LABELS']['DESCRIPTION_TOGGLE'])){
			$arTeacher['LABELS']['DESCRIPTION_TOGGLE'] = static::getMessage('ACRIT_EXP_TEACHER_DESCRIPTION_TOGGLE');
		}
		
		# Labels
		$arLabels = [
			'START' => 'start',
			'PREV' => 'prev',
			'NEXT' => 'next',
			'FINISH' => 'finish',
			'CLOSE' => 'close',
			'CONFIRM_EXIT' => 'confirmExit',
			'DESCRIPTION_TOGGLE' => 'descriptionToggle',
		];
		foreach($arLabels as $strKey => $strKeyJs){
			$arTeacherJson['labels'][$strKeyJs] = isset($arTeacher['LABELS'][$strKey]) 
				? $arTeacher['LABELS'][$strKey]
				: static::getMessage('ACRIT_EXP_TEACHER_'.$strKey);
		}
		
		# Callbacks
		if(strlen($arTeacher['CALLBACK_START'])) {
			$arTeacherJson['callbackStart'] = static::writeFunction($arTeacher['CALLBACK_START']);
		}
		if(strlen($arTeacher['CALLBACK_FINISH'])) {
			$arTeacherJson['callbackFinish'] = static::writeFunction($arTeacher['CALLBACK_FINISH']);
		}
		if(strlen($arTeacher['CALLBACK_CLOSE'])) {
			$arTeacherJson['callbackClose'] = static::writeFunction($arTeacher['CALLBACK_CLOSE']);
		}
		
		# Steps
		if(is_array($arTeacher['STEPS'])){
			$arKeys = [
				'ID' => 'ID',
				'ELEMENTS' => 'elements',
				'CALLBACK_ELEMENTS' => 'callbackElements',
				'ACCESSIBLE' => 'accessible',
				'TAB' => 'tabId',
				'SUB_TAB' => 'subTabId',
				'BUTTON_VISIBLE' => 'buttonVisible',
				'CALLBACK_BUTTON_VISIBLE' => 'callbackButtonVisible',
				'CALLBACK_IN' => 'callbackIn',
				'CALLBACK_OUT' => 'callbackOut',
				'CALLBACK_BEFORE' => 'callbackBefore',
				'CALLBACK_AFTER' => 'callbackAfter',
				'CALLBACK_SKIP' => 'callbackSkip',
				'TITLE' => 'title',
				'DESCRIPTION' => 'description',
				'CSS' => 'cssStyles',
			];
			foreach($arTeacher['STEPS'] as $strStep => $arStep){
				$arJsStep = [
					'code' => $strStep,
				];
				foreach($arKeys as $strKey => $strKeyJs){
					if(array_key_exists($strKey, $arStep)){
						if(preg_match('#^CALLBACK_#i', $strKey) || $strKey == 'ELEMENTS'){
							if($arStep[$strKey] === true || $arStep[$strKey] === false || $arStep[$strKey] === null){
								$arJsStep[$strKeyJs] = $arStep[$strKey];
							}
							else{
								$arJsStep[$strKeyJs] = static::writeFunction($arStep[$strKey]);
							}
						}
						else{
							$arJsStep[$strKeyJs] = $arStep[$strKey];
						}
					}
				}
				$arTeacherJson['steps'][] = $arJsStep;
			}
		}
		
		# System flags
		$arTeacherJson['tabControlName'] = strlen($arTeacher['TAB_CONTROL']) ? $arTeacher['TAB_CONTROL'] : null;
		$arTeacherJson['closeWindows'] = $arTeacher['CLOSE_WINDOWS'] == 'Y' ? 'Y' : 'N';
		
		# End...
		$intJsonParams = JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;
		$strJs = \Acrit\Core\Json::encode($arTeacherJson, $intJsonParams);
		$strJs = static::replaceFunction($strJs);
		if(!Helper::isUtf()){
			$strJs = Helper::convertEncoding($strJs, 'UTF-8', 'CP1251');
		}
		$arTeacher['JS'] = $strJs;
		return $strJs;
	}
	
	/**
	 *	
	 */
	public static function writeFunction($strFunctionCode){
		return static::FUNC_BEGIN.$strFunctionCode.static::FUNC_END;
	}
	
	/**
	 *	
	 */
	public static function replaceFunction($strJson){
		$strPatternBegin = preg_quote(static::FUNC_QUOTE.static::FUNC_BEGIN, '#');
		$strPatternEnd = preg_quote(static::FUNC_END.static::FUNC_QUOTE, '#');
		$strPattern = sprintf('#%s(.*?)%s#i', $strPatternBegin, $strPatternEnd);
		$strJson = preg_replace_callback($strPattern, function($arMatch){
			$arReplace = ['\n' => "\n", '\r' => "\r", '\t' => "\t", '\v' => "\v"];
			$strResult = str_replace(array_keys($arReplace), array_values($arReplace), $arMatch[1]);
			$strResult = stripslashes($strResult);
			return $strResult;
		}, $strJson);
		return str_replace($arReplace, '', $strJson);
	}
	
	/**
	 * Get lang message
	 */
	public static function getMessage($strLangKey, $arReplace=[]){
		$strPhrase = 'ACRIT_EXP_TEACHER_'.$strLangKey;
		$strMessage = Helper::getMessage($strPhrase, $arReplace);
		if(empty($strMessage)) {
			$strMessage = Helper::getMessage($strLangKey, $arReplace);
		}
		return $strMessage;
	}
	
	/**
	 *	Add js to admin page
	 */
	public static function addJs(){
		\Bitrix\Main\Page\Asset::getInstance()->addJs('/bitrix/js/'.ACRIT_CORE.'/teacher/jquery.acrit.teacher.js');
	}
	
	/**
	 *	Add css to admin page
	 */
	public static function addCss(){
		$GLOBALS['APPLICATION']->setAdditionalCss('/bitrix/js/'.ACRIT_CORE.'/teacher/jquery.acrit.teacher.css');
	}

}
