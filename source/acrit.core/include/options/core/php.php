<?
namespace Acrit\Core\Export;

use \Acrit\Core\Helper,
	\Acrit\Core\Cli;

Helper::loadMessages(__FILE__);

return [
	'NAME' => Helper::getMessage('ACRIT_CORE_OPTION_GROUP_EXPORT'),
	'OPTIONS' => [
		'php_path' => [
			'NAME' => Helper::getMessage('ACRIT_CORE_OPTION_PHP_PATH'),
			'HINT' => Helper::getMessage('ACRIT_CORE_OPTION_PHP_PATH_HINT'),
			'ATTR' => 'size="30" maxlength="255"',
			'TYPE' => 'text',
			'HEAD_DATA' => function($obOptions){
				?>
				<script>
				$(document).delegate('input[data-role="check-php-path"]', 'click', function(e){
					var phpPath = $('#acrit_core_option_php_path').val();
					if(phpPath.length) {
						phpPathBase64 = encodeURIComponent(btoa(phpPath));
						acritCoreAjax('check_php_path', 'php_path='+phpPathBase64, function(JsonResult, textStatus, jqXHR){
							if(JsonResult.Success){
								alert(JsonResult.Message);
							}
							else{
								alert(JsonResult.Message);
							}
						}, function(jqXHR){
							alert('Error!');
						}, false);
					}
				});
				$(document).delegate('[data-role="php-paths"] a', 'click', function(e){
					e.preventDefault();
					$('input[type=text][name=php_path]').val($(this).text());
				});
				</script>
				<?
			},
			'CALLBACK_MORE' => function($obOptions, $arOption){
				?>
				<input type="button" data-role="check-php-path" 
					value="<?=Helper::getMessage('ACRIT_CORE_OPTION_PHP_PATH_CHECK');?>" />
				<?
			},
			'CALLBACK_BOTTOM' => function($obOptions, $arOption){
				?>
				<?if(Cli::isExec()):?>
					<?$arPaths = Cli::getPotentialPhpPaths();?>
					<?if(!empty($arPaths)):?>
						<tr>
							<td style="padding-top:0;"></td>
							<td style="padding-top:0;" data-role="php-paths">
								<?
								foreach($arPaths as $key => $strPath){
									$arPaths[$key] = '<a href="javascript:void(0);" class="acrit-inline-link">'.$strPath.'</a>';
								}
								Helper::showNote(Helper::getMessage('ACRIT_CORE_OPTION_PHP_PATH_POTENTIAL', [
									'#PHP_PATHS#' => implode(', ', $arPaths),
								]), true);
								?>
							</td>
						</tr>
					<?endif?>
				<?endif?>
				<?
			}
		],
		'php_mbstring' => [
			'NAME' => Helper::getMessage('ACRIT_CORE_OPTION_MBSTRING'),
			'HINT' => Helper::getMessage('ACRIT_CORE_OPTION_MBSTRING_HINT'),
			'TYPE' => 'checkbox',
		],
		'php_config' => [
			'NAME' => Helper::getMessage('ACRIT_CORE_OPTION_CONFIG'),
			'HINT' => Helper::getMessage('ACRIT_CORE_OPTION_CONFIG_HINT'),
			'ATTR' => 'size="60" maxlength="255"',
			'TYPE' => 'text',
		],
		'php_add_site' => [
			'NAME' => Helper::getMessage('ACRIT_CORE_OPTION_ADD_SITE'),
			'HINT' => Helper::getMessage('ACRIT_CORE_OPTION_ADD_SITE_HINT'),
			'TYPE' => 'checkbox',
		],
		'php_output_stdout' => [
			'NAME' => Helper::getMessage('ACRIT_CORE_OPTION_OUTPUT_STDOUT'),
			'HINT' => Helper::getMessage('ACRIT_CORE_OPTION_OUTPUT_STDOUT_HINT'),
			'TYPE' => 'checkbox',
		],
		'warn_if_root' => [
			'NAME' => Helper::getMessage('ACRIT_CORE_OPTION_WARN_IF_ROOT'),
			'HINT' => Helper::getMessage('ACRIT_CORE_OPTION_WARN_IF_ROOT_HINT'),
			'TYPE' => 'checkbox',
		],
		'disable_crontab_set' => [
			'NAME' => Helper::getMessage('ACRIT_CORE_OPTION_DISABLE_CRONTAB_SET'),
			'HINT' => Helper::getMessage('ACRIT_CORE_OPTION_DISABLE_CRONTAB_SET_HINT'),
			'TYPE' => 'checkbox',
		],
	],
];
?>