<?IncludeModuleLangFile( __FILE__ );?>

<?$use_remarketing = $arProfile["USE_REMARKETING"] == "Y" ? 'checked="checked"' : "";?>                                     

<tr class="heading" align="center">
    <td colspan="2"><b><?=GetMessage( "KIT_EXPORTPRO_OP_GOOGLE_TITLE" )?></b></td>
</tr>
<tr>
    <td width="40%" class="adm-detail-content-cell-l">
        <span id="hint_PROFILE[GOOGLE_GOOGLEFEED]"></span><script type="text/javascript">BX.hint_replace( BX( 'hint_PROFILE[GOOGLE_GOOGLEFEED]' ), '<?=GetMessage( "KIT_EXPORTPRO_OP_GOOGLE_GOOGLEFEED_HELP" )?>' );</script>
        <label for="PROFILE[GOOGLE_GOOGLEFEED]"><b><?=GetMessage( "KIT_EXPORTPRO_OP_GOOGLE_GOOGLEFEED" )?></b> </label>
    </td>
    <td width="60%" class="adm-detail-content-cell-r">
        <input type="text" size="30" name="PROFILE[GOOGLE_GOOGLEFEED]" value="<?=$arProfile["GOOGLE_GOOGLEFEED"];?>" />
    </td>
</tr>
<tr>
    <td width="40%" class="adm-detail-content-cell-l">
        <span id="hint_PROFILE[USE_REMARKETING]"></span><script type="text/javascript">BX.hint_replace( BX( 'hint_PROFILE[USE_REMARKETING]' ), '<?=GetMessage( "KIT_EXPORTPRO_USE_REMARKETING_HELP" )?>' );</script>
        <label for="PROFILE[USE_REMARKETING]"><b><?=GetMessage( "KIT_EXPORTPRO_USE_REMARKETING" )?></b> </label>
    </td>
    <td width="60%" class="adm-detail-content-cell-r">
        <input type="checkbox" name="PROFILE[USE_REMARKETING]" value="Y" <?=$use_remarketing?>/>
    </td>
</tr>