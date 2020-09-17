<?
namespace Acrit\Core\Export\Plugins;

use
	\Acrit\Core\Helper;

$strTLang = 'TEACHER_';

$arTeacher = [
	'DEBUG' => false,
	'CODE' => 'EXPORT_OZON_NEW_API',
	'NAME' => static::getMessage($strTLang.'NAME'),
	'TITLE' => static::getMessage($strTLang.'TITLE', ['#NAME#' => static::getMessage('NAME')]),
	'SPLASH_SCREEN' => [
		'DESCRIPTION' => static::getMessage($strTLang.'SPLASH_SCREEN_DESCRIPTION'),
		'CSS' => '',
	],
	'TAB_CONTROL' => 'AcritExpProfile',
	'CLOSE_WINDOWS' => 'Y',
	'STEPS' => [
		'PLUGIN_SETTINGS' => null,
		'OZON_AUTH_DATA' => [
			'ELEMENTS' => '$("input[data-role=\"acrit_exp_ozon_new_client_id\"]")
				.add("input[data-role=\"acrit_exp_ozon_new_api_key\"]")
				.add("input[data-role=\"acrit_exp_ozon_new_access_check\"]")',
			'ACCESSIBLE' => 'Y',
			'TAB' => 'general',
			'AFTER' => 'FORMAT',
		],
		'OZON_DESCRIPTION_HOWTO' => [
			'ELEMENTS' => '$("div[data-role=\"ozon_description_hotwo\"]")',
			'ACCESSIBLE' => 'Y',
			'TAB' => 'general',
			'CSS' => '
				div[data-role="ozon_description_hotwo"] {
					background:#f5f9f9;
					padding:4px 10px;
				}
			',
			'AFTER' => 'OZON_AUTH_DATA',
		],
		'OZON_DESCRIPTION_RECOMMENDATIONS' => [
			'ELEMENTS' => '$("div[data-role=\"ozon_description_recommendations\"]")',
			'ACCESSIBLE' => 'Y',
			'TAB' => 'general',
			'CSS' => '
				div[data-role="ozon_description_recommendations"] {
					background:#f5f9f9;
					padding:4px 10px;
				}
			',
			'AFTER' => 'OZON_DESCRIPTION_HOWTO',
		],
		'OZON_DESCRIPTION_NUANCES' => [
			'ELEMENTS' => '$("div[data-role=\"ozon_description_nuances\"]")',
			'ACCESSIBLE' => 'Y',
			'TAB' => 'general',
			'CSS' => '
				div[data-role="ozon_description_nuances"] {
					background:#f5f9f9;
					padding:4px 10px;
				}
			',
			'AFTER' => 'OZON_DESCRIPTION_RECOMMENDATIONS',
		],
		'SUBTAB_CATEGORIES' => [
			'ELEMENTS' => '$("#view_tab_subtab_categories")',
			'AFTER' => 'OZON_DESCRIPTION_NUANCES',
			'ACCESSIBLE' => 'Y',
			'TAB' => 'structure',
			'SUB_TAB' => 'subtab_categories',
		],
		'CATEGORIES_REDEFINITIONS' => null,
		'OZON_CATEGORY_REDEFINITIONS_BUTTON' => [
			'ELEMENTS' => '$("input[data-role=\"categories-redefinition-button\"]")',
			'ACCESSIBLE' => 'Y',
			'TAB' => 'structure',
			'SUB_TAB' => 'subtab_categories',
			'CALLBACK_IN' => 'function(options, prevStepData, currStepData){
				this.handler = this.addHandler(currStepData.elements, "click", function(){
					this.removeHandler(currStepData.elements, "click", this.handler);
					if(this.goingNext){
						this.goNextDelay(10);
					}
				});
			}',
			'CALLBACK_OUT' => 'function(options, currStepData, nextStepData){
				this.removeHandler(currStepData.elements, "click", this.handler);
				delete this.handler;
			}',
			'AFTER' => 'CATEGORIES_UPDATE',
		],
		'OZON_CATEGORY_REDEFINITIONS_POPUP' => [
			'CALLBACK_ELEMENTS' => 'function(options, stepData){
				return $(AcritExpPopupCategoriesRedefinition.DIV);
			}',
			'ACCESSIBLE' => 'N',
			'SUB_TAB' => 'subtab_categories',
			'CALLBACK_IN' => 'function(options, currStepData, nextStepData){
				if(!AcritExpPopupCategoriesRedefinition.isOpen){
					$("input[data-role=\"categories-redefinition-button\"]").trigger("click");
				}
				this.handler = this.addHandler(window, "onWindowClose", function(popup){
					if(popup == AcritExpPopupCategoriesRedefinition && AcritExpPopupCategoriesRedefinition.isOpen){
						this.removeHandler(AcritExpPopupCategoriesRedefinition, "onWindowClose", this.handler);
						if(this.goingNext){
							this.goNextDelay(10);
						}
					}
				});
			}',
			'CALLBACK_BEFORE' => 'function(options, stepData){
				if(AcritExpPopupCategoriesRedefinition.isOpen && !!$(".acrit-exp-table-categories-redefinition").length){
					return true;
				}
				return false;
			}',
			'CALLBACK_OUT' => 'function(options, currStepData, nextStepData){
				this.removeHandler(window, "onWindowClose", this.handler);
				delete this.handler;
				AcritExpPopupCategoriesRedefinition.Close();
			}',
			'AFTER' => 'OZON_CATEGORY_REDEFINITIONS_BUTTON',
		],
		'OZON_CATEGORY_ATTRIBUTES_UPDATE' => [
			'ELEMENTS' => '$("input[data-role=\"categories-update-attributes-start\"]")',
			'ACCESSIBLE' => 'Y',
			'TAB' => 'structure',
			'SUB_TAB' => 'subtab_categories',
			'AFTER' => 'CATEGORIES_UPDATE',
			'CSS' => '
				div[data-role="ozon_description_nuances"] {
					background:#f5f9f9;
					padding:4px 10px;
				}
			',
			'AFTER' => 'OZON_CATEGORY_REDEFINITIONS_POPUP',
		],
		'OZON_ALLOWED_VALUES' => [
			'ELEMENTS' => '$("tr[data-role=\"field_row\"] span > img[src*=\"icon-warning\"]").first()',
			'ACCESSIBLE' => 'Y',
			'TAB' => 'structure',
			'SUB_TAB' => 'fields_product',
			'CALLBACK_IN' => 'function(options, prevStepData, currStepData){
				this.handler = this.addHandler(currStepData.elements, "click", function(){
					this.removeHandler(currStepData.elements, "click", this.handler);
					if(this.goingNext){
						this.goNextDelay(10);
					}
				});
			}',
			'CALLBACK_OUT' => 'function(options, currStepData, nextStepData){
				this.removeHandler(currStepData.elements, "click", this.handler);
				delete this.handler;
			}',
			'AFTER' => 'OZON_CATEGORY_ATTRIBUTES_UPDATE',
		],
		'OZON_ALLOWED_VALUES_POPUP' => [
			'CALLBACK_ELEMENTS' => 'function(options, stepData){
				return $(AcritPopupHint.DIV);
			}',
			'ACCESSIBLE' => 'Y',
			'SUB_TAB' => 'subtab_categories',
			'CALLBACK_IN' => 'function(options, currStepData, nextStepData){
				$("tr[data-role=\"field_row\"] span > img[src*=\"icon-warning\"]").first().trigger("click");
				this.handler = this.addHandler(window, "onWindowClose", function(popup){
					if(popup == AcritPopupHint && AcritPopupHint.isOpen){
						this.removeHandler(AcritPopupHint, "onWindowClose", this.handler);
						if(this.goingNext){
							this.goNextDelay(10);
						}
					}
				});
			}',
			'CALLBACK_BEFORE' => 'function(options, stepData){
				if(AcritPopupHint.isOpen && !!$("div[data-role=\"allowed-values-filter-results\"]").length){
					return true;
				}
				return false;
			}',
			'CALLBACK_OUT' => 'function(options, currStepData, nextStepData){
				this.removeHandler(window, "onWindowClose", this.handler);
				delete this.handler;
				AcritPopupHint.Close();
			}',
			'AFTER' => 'OZON_ALLOWED_VALUES',
		],
		'OZON_TASKS_LOG' => [
			'ELEMENTS' => '$("div[data-role=\"ozon_tasks_log\"]")',
			'ACCESSIBLE' => 'N',
			'TAB' => 'log',
			'CSS' => '
				div[data-role="ozon_tasks_log"] {
					background:#f5f9f9;
					padding:4px 10px;
				}
			',
			'AFTER' => 'HISTORY_WRAPPER',
		],
	],
];

foreach($arTeacher['STEPS'] as $strStep => &$arStep){
	if(is_array($arStep)){
		$arStep['TITLE'] = static::getMessage($strTLang.'STEP_NAME_'.$strStep);
		$arStep['DESCRIPTION'] = static::getMessage($strTLang.'STEP_DESC_'.$strStep);
	}
}
unset($arStep);

return $arTeacher;
