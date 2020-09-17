<?
namespace Acrit\Core\Export;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Cli;

Loc::loadMessages(__FILE__);

$GLOBALS['APPLICATION']->addHeadScript('/bitrix/js/acrit.core/jquery.textchange.min.js');

$bMultithreadingSupported = Cli::isMultithreadingSupported() === true;
$strCoreCount = $GLOBALS['ACRIT_CORE_CPU_CORE_COUNT'] = Cli::getCpuCoresCount();
$bMultithreadingAvailable = in_array($this->strModuleId, array_slice(\Acrit\Core\Export\Exporter::getExportModules(), -2));
$GLOBALS['ACRIT_EXP_MULTITHREADING_SUPPORTED'] = &$bMultithreadingSupported;
$GLOBALS['ACRIT_EXP_MULTITHREADING_AVAILABLE'] = &$bMultithreadingAvailable;

return [
	'NAME' => Loc::getMessage('ACRIT_CORE_OPTION_GROUP_EXPORT'),
	'OPTIONS' => [
		'time_step' => [
			'NAME' => Loc::getMessage('ACRIT_CORE_OPTION_EXPORT_TIME_STEP'),
			'HINT' => Loc::getMessage('ACRIT_CORE_OPTION_EXPORT_TIME_STEP_HINT'),
			'ATTR' => 'size="10" maxlength="10"',
			'TYPE' => 'text',
			'HEAD_DATA' => function(){
				?>
				<script>
				$(document).ready(function(){
					$('#acrit_<?=end(explode('.', $this->strModuleId));?>_option_time_step').bind('textchange', function(){
						var timeStep = parseInt($(this).val()),
							row = $('tr#acrit-exp-row-warning-time-delay').hide(),
							max = 25;
						if(!isNaN(timeStep) && timeStep > 0 && timeStep > max) {
							row.show();
						}
					}).trigger('textchange');
				});
				</script>
				<?
			},
			'CALLBACK_BOTTOM' => function($obOptions, $arOption){
				?>
					<tr id="acrit-exp-row-warning-time-delay" style="display:none;">
						<td style="padding-top:0;"></td>
						<td style="padding-top:0;" data-role="php-paths">
							<?=\Acrit\Core\Helper::showNote(Loc::getMessage('ACRIT_CORE_OPTION_EXPORT_TIME_STEP_NOTICE'), true);?>
						</td>
					</tr>
				<?
			}
		],
		'time_delay' => [
			'NAME' => Loc::getMessage('ACRIT_CORE_OPTION_EXPORT_TIME_DELAY'),
			'HINT' => Loc::getMessage('ACRIT_CORE_OPTION_EXPORT_TIME_DELAY_HINT'),
			'ATTR' => 'size="10" maxlength="10"',
			'TYPE' => 'text',
		],
		'lock_time' => [
			'NAME' => Loc::getMessage('ACRIT_CORE_OPTION_LOCK_TIME'),
			'HINT' => Loc::getMessage('ACRIT_CORE_OPTION_LOCK_TIME_HINT'),
			'ATTR' => 'size="10" maxlength="10"',
			'TYPE' => 'text',
		],
		'multithreaded' => [
			'NAME' => Loc::getMessage('ACRIT_CORE_OPTION_MULTITHREADED'),
			'HINT' => Loc::getMessage('ACRIT_CORE_OPTION_MULTITHREADED_HINT'),
			'ATTR' => $bMultithreadingSupported && $bMultithreadingAvailable ? '' : 'disabled="disabled"',
			'TYPE' => 'checkbox',
			'HEAD_DATA' => function(){
				?>
				<script>
				$(document).delegate('tr#acrit_exp_option_multithreaded input[type=checkbox]', 'change', function(e){
					$('tr#acrit_exp_option_threads').toggle($(this).is(':checked') && !$(this).is('[disabled]'));
					$('tr#acrit_exp_option_elements_per_thread_cron').toggle($(this).is(':checked') && !$(this).is('[disabled]'));
					$('tr#acrit_exp_option_elements_per_thread_manual').toggle($(this).is(':checked') && !$(this).is('[disabled]'));
				});
				</script>
				<?
			},
			'CALLBACK_MORE' => function($arOption){
				ob_start();
				if($GLOBALS['ACRIT_EXP_MULTITHREADING_AVAILABLE']){
					if(!$GLOBALS['ACRIT_EXP_MULTITHREADING_SUPPORTED']){
						if(Cli::isWindows()){
							print Loc::getMessage('ACRIT_CORE_OPTION_MULTITHREADED_NOT_SUPPORTED_WINDOWS', [
								'#CORE_ID#' => ACRIT_CORE,
								'#LANGUAGE_ID#' => LANGUAGE_ID,
							]);
						}
						else{
							print Loc::getMessage('ACRIT_CORE_OPTION_MULTITHREADED_NOT_SUPPORTED', [
								'#CORE_ID#' => ACRIT_CORE,
								'#LANGUAGE_ID#' => LANGUAGE_ID,
							]);
						}
					}
				}
				else{
					print Loc::getMessage('ACRIT_CORE_OPTION_MULTITHREADED_NOT_AVAILABLE', [
						'#CORE_ID#' => ACRIT_CORE,
						'#LANGUAGE_ID#' => LANGUAGE_ID,
					]);
				}
				return ob_get_clean();
			},
			'CALLBACK_BOTTOM' => function($obOptions, $arOption){
				?>
					<?if(!$GLOBALS['ACRIT_EXP_MULTITHREADING_SUPPORTED']):?>
						<tr>
							<td style="padding-top:0;"></td>
							<td style="padding-top:0;" data-role="php-paths">
								<div>
									<a href="javascript:void(0);" class="acrit-inline-link"
										onclick="$(this).parent().next().toggle(); return false;">
										<?=Loc::getMessage('ACRIT_CORE_OPTION_MULTITHREADED_DETAILS');?>
									</a>
								</div>
								<div style="display:none">
									<p>
										<b><?=Loc::getMessage('ACRIT_CORE_OPTION_MULTITHREADED_COMMAND');?>:</b><br/>
										<?=Cli::$arError['COMMAND'];?>
									</p>
									<p>
										<b><?=Loc::getMessage('ACRIT_CORE_OPTION_MULTITHREADED_STDOUT');?>:</b><br/>
										<?=(strlen(Cli::$arError['STDOUT']) ? Cli::$arError['STDOUT'] : '&lt;empty&gt;');?>
									</p>
									<p>
										<b><?=Loc::getMessage('ACRIT_CORE_OPTION_MULTITHREADED_STDERR');?>:</b><br/>
										<?=(strlen(Cli::$arError['STDERR']) ? Cli::$arError['STDERR'] : '&lt;empty&gt;');?>
									</p>
								</div>
							</td>
						</tr>
					<?endif?>
				<?
			}
		],
		'threads' => [
			'NAME' => Loc::getMessage('ACRIT_CORE_OPTION_THREADS'),
			'HINT' => Loc::getMessage('ACRIT_CORE_OPTION_THREADS_HINT'),
			'ATTR' => 'size="10" maxlength="2"',
			'TYPE' => 'text',
			'HEAD_DATA' => function(){
				?>
				<script>
				$(document).ready(function(){
					$('#acrit_<?=end(explode('.', $this->strModuleId));?>_option_threads').bind('textchange', function(){
						var threads = parseInt($(this).val()),
							row = $('tr#acrit-exp-row-warning-thread').hide(),
							count = <?=IntVal($GLOBALS['ACRIT_CORE_CPU_CORE_COUNT']);?>;
						if($(this).is(':visible') && !isNaN(threads) && threads > 0 && count > 0 && threads > count) {
							row.show();
						}
					}).trigger('textchange');
				});
				</script>
				<?
			},
			'CALLBACK_MORE' => function($arOption){
				if($GLOBALS['ACRIT_CORE_CPU_CORE_COUNT']){
					return '&nbsp; '.Loc::getMessage('ACRIT_CORE_OPTION_THREADS_CORE_COUNT', array(
						'#CORE_COUNT#' => '<i>'.$GLOBALS['ACRIT_CORE_CPU_CORE_COUNT'].'</i>',
					));
				}
			},
			'CALLBACK_BOTTOM' => function($obOptions, $arOption){
				?>
					<tr id="acrit-exp-row-warning-thread" style="display:none;">
						<td style="padding-top:0;"></td>
						<td style="padding-top:0;" data-role="php-paths">
							<?=\Acrit\Core\Helper::showNote(Loc::getMessage('ACRIT_CORE_OPTION_THREADS_NOTICE'), true);?>
						</td>
					</tr>
				<?
			}
		],
		'elements_per_thread_cron' => [
			'NAME' => Loc::getMessage('ACRIT_CORE_OPTION_ELEMENTS_PER_THREAD_CRON'),
			'HINT' => Loc::getMessage('ACRIT_CORE_OPTION_ELEMENTS_PER_THREAD_CRON_HINT'),
			'ATTR' => 'size="10" maxlength="5"',
			'TYPE' => 'text',
			'HEAD_DATA' => function(){
				?>
				<script>
				$(document).ready(function(){
					$('#acrit_<?=end(explode('.', $this->strModuleId));?>_option_elements_per_thread_cron').bind('textchange', function(){
						var elements = parseInt($(this).val()),
							row = $('tr#acrit-exp-row-warning-elements-per-thread-cron').hide(),
							max = 1000;
						if($(this).is(':visible') && !isNaN(elements) && elements > 0 && elements > max) {
							row.show();
						}
					}).trigger('textchange');
				});
				</script>
				<?
			},
			'CALLBACK_BOTTOM' => function($obOptions, $arOption){
				?>
					<tr id="acrit-exp-row-warning-elements-per-thread-cron" style="display:none;">
						<td style="padding-top:0;"></td>
						<td style="padding-top:0;" data-role="php-paths">
							<?=\Acrit\Core\Helper::showNote(Loc::getMessage('ACRIT_CORE_OPTION_ELEMENTS_PER_THREAD_CRON_NOTICE'), true);?>
						</td>
					</tr>
				<?
			}
		],
		'elements_per_thread_manual' => [
			'NAME' => Loc::getMessage('ACRIT_CORE_OPTION_ELEMENTS_PER_THREAD_MANUAL'),
			'HINT' => Loc::getMessage('ACRIT_CORE_OPTION_ELEMENTS_PER_THREAD_MANUAL_HINT'),
			'ATTR' => 'size="10" maxlength="5"',
			'TYPE' => 'text',
			'HEAD_DATA' => function(){
				?>
				<script>
				$(document).ready(function(){
					$('#acrit_<?=end(explode('.', $this->strModuleId));?>_option_elements_per_thread_manual').bind('textchange', function(){
						var elements = parseInt($(this).val()),
							row = $('tr#acrit-exp-row-warning-elements-per-thread-manual').hide(),
							max = 100;
						if($(this).is(':visible') && !isNaN(elements) && elements > 0 && elements > max) {
							row.show();
						}
					}).trigger('textchange');
				});
				</script>
				<?
			},
			'CALLBACK_BOTTOM' => function($obOptions, $arOption){
				?>
					<tr id="acrit-exp-row-warning-elements-per-thread-manual" style="display:none;">
						<td style="padding-top:0;"></td>
						<td style="padding-top:0;" data-role="php-paths">
							<?=\Acrit\Core\Helper::showNote(Loc::getMessage('ACRIT_CORE_OPTION_ELEMENTS_PER_THREAD_MANUAL_NOTICE'), true);?>
						</td>
					</tr>
				<?
			}
		],
	],
];
?>