<?
namespace Acrit\Core\Export;

use
	\Acrit\Core\Helper;

Helper::loadMessages(__FILE__);
?>
<input type="hidden" data-role="allowed-values-current-field" value="<?=htmlspecialcharsbx($strField);?>" />
<table data-role="allowed-values-table">
	<tbody>
		<tr>
			<td>
				<input type="text" value="" data-role="allowed-values-filter-text" 
					placeholder="<?=static::getMessage('FILTER_PLACEHOLDER');?>" />
			</td>
		</tr>
		<tr>
			<td>
				<div data-role="allowed-values-filter-results">
					<?require __DIR__.'/filtered.php';?>
				</div>
			</td>
		</tr>
	</tbody>
</table>
