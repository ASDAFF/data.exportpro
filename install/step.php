<?if( !check_bitrix_sessid() ) return;?>
<?echo CAdminMessage::ShowNote( GetMessage( "KIT_EXPORTPRO_MODULE" ) );
/**
 * Copyright (c) 15/9/2020 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

IncludeModuleLangFile(__FILE__);
?>

<table id="install_instruction">
	<tr>
        <td>
            <?=GetMessage( "KIT_EXPORTPRO_RECOMMENDS" );?>
        </td>
    </tr>
    <tr class="">
		<td>
            <form action="/bitrix/admin/partner_modules.php" method="GET">
                <input type="submit" class="adm-btn adm-btn-save" value="<?=GetMessage( "MOD_BACK" )?>" />
            </form>  
			<form action="/bitrix/admin/partner_modules.php" method="GET">
				<input type="hidden" name="id" value="kit.exportpro">
                <input type="hidden" name="lang" value="<?=LANGUAGE_ID?>">
                <input type="hidden" name="install" value="Y">
                <input type="hidden" name="sessid" value="<?=bitrix_sessid()?>">
                <input type="hidden" name="step" value="2">
                <input type="submit" class="adm-btn adm-btn-save" value="<?=GetMessage( "MOD_INSTALL" )?>" />
			</form>
		</td>
	</tr>
</table>