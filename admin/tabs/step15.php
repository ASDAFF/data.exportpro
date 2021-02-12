<?php
require_once( $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php" );
IncludeModuleLangFile( __FILE__ );
?>

<tr class="heading">
    <td colspan="2"><?=GetMessage( "DATA_EXPORTPRO_FAQ_BASE" )?></td>
</tr>
<tr>

    <td valign="top" class="adm-detail-content-cell-r"><textarea cols="60" rows="6" name="ticket_text_proxy"
        id="ticket_text_proxy"></textarea>
        <textarea style="display:none" name="ticket_text_log" id="ticket_text_log">
            <b><?=GetMessage( "DATA_EXPORTPRO_LOG_STATISTICK" )?></b><br>
            <b><?=GetMessage( "DATA_EXPORTPRO_LOG_ALL" )?></b><br>
            <?=GetMessage( "DATA_EXPORTPRO_LOG_ALL_IB" )?> <?=$arProfile["LOG"]["IBLOCK"]?><br>
            <?=GetMessage( "DATA_EXPORTPRO_LOG_ALL_SECTION" )?> <?=$arProfile["LOG"]["SECTIONS"]?><br>
            <?=GetMessage( "DATA_EXPORTPRO_LOG_ALL_OFFERS" )?> <?=$arProfile["LOG"]["PRODUCTS"]?><br>
            <b><?=GetMessage( "DATA_EXPORTPRO_LOG_EXPORT" )?></b><br>
            <?=GetMessage( "DATA_EXPORTPRO_LOG_OFFERS_EXPORT" )?> <?=$arProfile["LOG"]["PRODUCTS_EXPORT"]?><br>
            <b><?=GetMessage( "DATA_EXPORTPRO_LOG_ERROR" )?></b><br>
            <?=GetMessage( "DATA_EXPORTPRO_LOG_ERR_OFFERS" )?> <?=$arProfile["LOG"]["PRODUCTS_ERROR"]?><br>
            <?if( file_exists( $_SERVER["DOCUMENT_ROOT"].$arProfile["LOG"]["FILE"] ) ):?>
                <?=GetMessage( "DATA_EXPORTPRO_LOG_FILE" )?> <?=$arProfile["LOG"]["FILE"]?><br>
            <?endif?>
        </textarea>
        </td>
</tr>
<tr>
	<td colspan="2">
		<?=GetMessage( "DATA_EXPORTPRO_RECOMMENDS" );?>
	</td>
</tr>