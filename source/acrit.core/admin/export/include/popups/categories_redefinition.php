<?
namespace Acrit\Core\Export;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper,
	\Acrit\Core\Export\CategoryRedefinitionTable as CategoryRedefinition;
	

Loc::loadMessages(__FILE__);

# Save values
if($bSaveRedefinition) {
	if(is_array($arPost['s'])){
		$arSectionDefinitions = $arPost['s'];
		if(!Helper::isUtf()){
			$arSectionDefinitions = Helper::convertEncoding($arSectionDefinitions, 'UTF-8', 'CP1251');
		}
		$arActualSectionsID = array();
		foreach($arSectionDefinitions as $intSectionID => $strSectionName){
			$arActualSectionsID[] = $intSectionID;
			$strSectionName = trim($strSectionName);
			#if(!strlen($strSectionName)) {
			#	continue;
			#}
			$arFields = array(
				'SECTION_NAME' => $strSectionName,
			);
			$arQuery = [
				'filter' => array(
					'PROFILE_ID' => $intProfileID,
					'IBLOCK_ID' => $intIBlockID,
					'SECTION_ID' => $intSectionID,
				),
				'limit' => '1',
				'select' => array(
					'ID',
					'SECTION_NAME',
				),
			];
			#$resCategoryRedefinition = CategoryRedefinition::getList($arQuery);
			$resCategoryRedefinition = Helper::call($strModuleId, 'CategoryRedefinition', 'getList', [$arQuery]);
			if($arCategoryRedefinition = $resCategoryRedefinition->fetch()){
				if($arCategoryRedefinition['SECTION_NAME'] != $strSectionName) {
					#CategoryRedefinition::update($arCategoryRedefinition['ID'], $arFields);
					Helper::call($strModuleId, 'CategoryRedefinition', 'update', [$arCategoryRedefinition['ID'], $arFields]);
				}
			}
			else {
				$arFields = array_merge($arFields, array(
					'PROFILE_ID' => $intProfileID,
					'IBLOCK_ID' => $intIBlockID,
					'SECTION_ID' => $intSectionID,
				));
				#$obResult = CategoryRedefinition::add($arFields);
				$obResult = Helper::call($strModuleId, 'CategoryRedefinition', 'add', [$arFields]);
			}
		}
		# Delete old
		$arQuery = [
			'filter' => array(
				'PROFILE_ID' => $intProfileID,
				'IBLOCK_ID' => $intIBlockID,
				'!SECTION_ID' => $arActualSectionsID,
			),
			'select' => array(
				'ID',
			),
		];
		#$resCategoryRedefinition = CategoryRedefinition::getList($arQuery);
		$resCategoryRedefinition = Helper::call($strModuleId, 'CategoryRedefinition', 'getList', [$arQuery]);
		while($arCategoryRedefinition = $resCategoryRedefinition->fetch()){
			#CategoryRedefinition::delete($arCategoryRedefinition['ID']);
			Helper::call($strModuleId, 'CategoryRedefinition', 'delete', [$arCategoryRedefinition['ID']]);
		}
	}
	elseif ($arPost['clear_all']=='Y'){
		$arQuery = [
			'filter' => array(
				'PROFILE_ID' => $intProfileID,
				'IBLOCK_ID' => $intIBlockID,
			),
			'select' => array(
				'ID',
			),
		];
		#$resCategoryRedefinition = CategoryRedefinition::getList($arQuery);
		$resCategoryRedefinition = Helper::call($strModuleId, 'CategoryRedefinition', 'getList', [$arQuery]);
		while($arCategoryRedefinition = $resCategoryRedefinition->fetch()){
			#CategoryRedefinition::delete($arCategoryRedefinition['ID']);
			Helper::call($strModuleId, 'CategoryRedefinition', 'delete', [$arCategoryRedefinition['ID']]);
		}
	}
	// Remove old generated data
	#ExportData::deleteGeneratedData($intProfileID, $intIBlockID);
	Helper::call($strModuleId, 'ExportData', 'deleteGeneratedData', [$intProfileID, $intIBlockID]);
	return;
}

$arCategories = explode(',', $arPost['categories_id']);
Helper::arrayRemoveEmptyValues($arCategories);
$strMode = $arPost['categories_mode'];
$strSource = $arPost['categories_source'];

# Get all sections for iblock
$arSectionsAll = array();
$arFilter = array(
	'IBLOCK_ID' => $intIBlockID,
	'CHECK_PERMISSIONS' => 'N',
);
$arSelect = array(
	'ID',
	'NAME',
	'DEPTH_LEVEL',
	'IBLOCK_SECTION_ID',
);
$resSectionsAll = \CIBlockSection::getList(array('LEFT_MARGIN'=>'ASC'), $arFilter, false, $arSelect);
while($arSection = $resSectionsAll->getNext()){
	$arSectionsAll[$arSection['ID']] = $arSection;
}

# Function for recursive
function walkSection(&$arSection, $callback, &$arParams, $arChain=array()){ # ToDo: перенести в отдельный класс по Redefinition
	if(is_array($arSection)) {
		$arChain[] = $arSection['ID'];
		call_user_func_array($callback, array(&$arSection, &$arParams, $arChain));
		if(is_array($arSection['SECTIONS'])) {
			foreach($arSection['SECTIONS'] as &$arSubSection){
				walkSection($arSubSection, $callback, $arParams, $arChain);
			}
		}
	}
}

# PreProcess input data
switch($strSource){
	case 'selected':
		foreach($arSectionsAll as $intSectionID => $arSection){
			if(!in_array($intSectionID, $arCategories)){
				unset($arSectionsAll[$intSectionID]);
			}
		}
		break;
	case 'selected_with_subsections':
		$arTree = Helper::sectionsArrayToTree($arSectionsAll);
		$arParams = array(
			'CATEGORIES_SELECTED' => $arCategories,
			'CATEGORIES_RESULT' => array(),
		);
		#
		foreach($arTree as $key => &$arSection){
			walkSection($arSection, function(&$arSection, &$arParams, $arChain){
				if(is_array($arChain) && count(array_intersect($arParams['CATEGORIES_SELECTED'], $arChain))) {
					$arParams['CATEGORIES_RESULT'][] = $arSection['ID'];
				}
			}, $arParams);
		}
		#
		if(is_array($arParams['CATEGORIES_RESULT'])) {
			foreach($arSectionsAll as $intSectionID => $arSection){
				if(!in_array($intSectionID, $arParams['CATEGORIES_RESULT'])){
					unset($arSectionsAll[$intSectionID]);
				}
			}
		}
		break;
	default: // all
		break;
}

$arCategoryRedefinitionAll = array();
$arQuery = [
	'filter' => array(
		'PROFILE_ID' => $intProfileID,
		'IBLOCK_ID' => $intIBlockID,
	),
	'select' => array(
		'SECTION_ID',
		'SECTION_NAME',
	),
];
#$resCategoryRedefinition = CategoryRedefinition::getList($arQuery);
$resCategoryRedefinition = Helper::call($strModuleId, 'CategoryRedefinition', 'getList', [$arQuery]);
while($arCategoryRedefinition = $resCategoryRedefinition->fetch()){
	$arCategoryRedefinitionAll[$arCategoryRedefinition['SECTION_ID']] = $arCategoryRedefinition;
}

$bStrictMode = $strMode == CategoryRedefinition::MODE_STRICT;

?>

<div>
	<table class="acrit-exp-table-categories-redefinition">
		<thead>
			<tr>
				<th><?=Loc::getMessage('ACRIT_EXP_POPUP_CATEGORIES_REDEFINITION_COLUMN_ID');?></th>
				<th><?=Loc::getMessage('ACRIT_EXP_POPUP_CATEGORIES_REDEFINITION_COLUMN_OLDNAME');?></th>
				<th><?=Loc::getMessage('ACRIT_EXP_POPUP_CATEGORIES_REDEFINITION_COLUMN_NEWNAME');?></th>
				<?if($bStrictMode):?>
					<th></th>
				<?endif?>
			</tr>
		</thead>
		<tbody>
			<?foreach($arSectionsAll as $arSection):?>
				<tr data-depth="<?=$arSection['DEPTH_LEVEL'];?>">
					<td>
						<?=$arSection['ID'];?>
					</td>
					<td>
					<?
						print str_repeat('.&nbsp;&nbsp;', $arSection['DEPTH_LEVEL']-1);
					?>
						<?=$arSection['NAME'];?>
					</td>
					<td>
						<input type="text" name="s[<?=$arSection['ID'];?>]" <?if($bStrictMode):?>readonly="readonly"<?endif?>
							value="<?=htmlspecialcharsbx($arCategoryRedefinitionAll[$arSection['ID']]['SECTION_NAME']);?>"
							title="<?=htmlspecialcharsbx($arCategoryRedefinitionAll[$arSection['ID']]['SECTION_NAME']);?>"
							data-role="categories-redefinition-text"
						/>
						<a href="#" class="acrit-exp-table-categories-redefinition-clear" 
							data-role="categories-redefinition-button-clear">&times;</a>
					</td>
					<?if($bStrictMode):?>
						<td>
							<input type="button" value="" data-role="categories-redefinition-button-select"
								data-iblock-id="<?=$intIBlockID;?>" data-section-id="<?=$arSection['ID'];?>"
							/>
						</td>
					<?endif?>
				</tr>
			<?endforeach?>
		</tbody>
	</table>
</div>
<br/>

