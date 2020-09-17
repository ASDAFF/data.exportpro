<?
\CAdminFileDialog::showScript(Array(
	'event' => 'AcritExpGoodsStorageDirectorySelect',
	'arResultDest' => array('FUNCTION_NAME' => 'acritExpGoodsStorageDirectorySelectCallback'),
	'arPath' => array(),
	'select' => 'D',
	'operation' => 'S',
	'showUploadTab' => true,
	'showAddToMenuTab' => false,
	'fileFilter' => $this->strFileExt,
	'allowAllFiles' => true,
	'saveConfig' => true,
))
?>
<table class="acrit-exp-goods-storage-directory-internal" style="border-collapse:collapse;">
	<tbody>
		<tr>
			<td><input type="text" name="PROFILE[PARAMS][STORAGE_DIRECTORY]" size="50" 
				id="<?=$this->getInputID('STORAGE_DIRECTORY');?>"
				value="<?=htmlspecialcharsbx($this->arParams['STORAGE_DIRECTORY']);?>"
				placeholder="<?=static::getMessage('SETTINGS_FILE_PLACEHOLDER');?>" /></td>
			<td><input type="button" value="..." onclick="AcritExpGoodsStorageDirectorySelect()" 
				style="height:27px; margin:0;" /></td>
		</tr>
	</tbody>
</table>
<script>
function acritExpGoodsStorageDirectorySelectCallback(File,Path,Site){
	var PathCorrected = Path+(Path!='/'?'/':'');
	$('#<?=$this->getInputID('STORAGE_DIRECTORY');?>').val(PathCorrected);
}
</script>
