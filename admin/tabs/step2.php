<?php
IncludeModuleLangFile(__FILE__);

$view_catalog1 = $arProfile["VIEW_CATALOG"] == "Y" ? 'checked="checked"' : "";
$check_include1 = $arProfile["CHECK_INCLUDE"] == "Y" ? 'checked="checked"' : "";
$use_sku1 = $arProfile["USE_SKU"] == "Y" ? 'checked="checked"' : "";
?>                                                    

<tr class="heading" align="center">
    <td colspan="2"><b><?=GetMessage( "DATA_EXPORTPRO_IBLOCK_SECTION_SHOW" )?></b></td>
</tr>
<tr>
    <td width="40%" class="adm-detail-content-cell-l">
        <span id="hint_PROFILE[VIEW_CATALOG]"></span><script type="text/javascript">BX.hint_replace( BX( 'hint_PROFILE[VIEW_CATALOG]' ), '<?=GetMessage( "DATA_EXPORTPRO_STEP1_ONLY_CATALOG_HELP" )?>' );</script>
        <label for="PROFILE[VIEW_CATALOG]"><?=GetMessage( "DATA_EXPORTPRO_STEP1_ONLY_CATALOG" )?></label>
    </td>
    <td width="60%" class="adm-detail-content-cell-r">
        <input type="checkbox" name="PROFILE[VIEW_CATALOG]" <?=$view_catalog1?> value="Y" />
        <i><?=GetMessage( "DATA_EXPORTPRO_STEP1_ONLY_CATALOG_DESC" )?></i>
    </td>
</tr>
<tr>
    <td width="40%" class="adm-detail-content-cell-l">
        <span id="hint_PROFILE[CHECK_INCLUDE]"></span><script type="text/javascript">BX.hint_replace( BX( 'hint_PROFILE[CHECK_INCLUDE]' ), '<?=GetMessage( "DATA_EXPORTPRO_STEP1_CHECK_INCLUDE_HELP" )?>' );</script>
        <label for="PROFILE[CHECK_INCLUDE]"><?=GetMessage( "DATA_EXPORTPRO_STEP1_CHECK_INCLUDE" )?></label>
    </td>
    <td width="60%" class="adm-detail-content-cell-r">
        <input type="checkbox" name="PROFILE[CHECK_INCLUDE]" <?=$check_include1?> value="Y" />
        <i><?=GetMessage( "DATA_EP1_CHECK_INCLUDE_DESC" )?></i>
    </td>
</tr>
<tr>
    <td width="40%" class="adm-detail-content-cell-l">
        <span id="hint_PROFILE[USE_SKU]"></span><script type="text/javascript">BX.hint_replace( BX( 'hint_PROFILE[USE_SKU]' ), '<?=GetMessage( "DATA_EXPORTPRO_STEP1_USE_SKU_HELP" )?>' );</script>
        <label for="PROFILE[USE_SKU]"><?=GetMessage( "DATA_EXPORTPRO_STEP1_USE_SKU" )?></label>
    </td>
    <td width="60%" class="adm-detail-content-cell-r">
        <input type="checkbox" name="PROFILE[USE_SKU]" <?=$use_sku1?> value="Y" />
    </td>
</tr>
<tr class="heading" align="center">
    <td colspan="2"><b><?=GetMessage( "DATA_EXPORTPRO_IBLOCK_SELECT" )?></b></td>
</tr>
<tr>
	<?$ibtypes = $profileUtils->GetIBlockTypes(
        $arProfile["LID"],
        $arProfile["VIEW_CATALOG"] == "Y",
        false
	);?>
	<td colspan="2">
		<p id="ibtype_select_block">
			<select multiple="multiple" name="PROFILE[IBLOCK_TYPE_ID][]">
				<?foreach( $ibtypes as $id => $type ){
                    $selected = "";
					if( is_array( $arProfile["IBLOCK_TYPE_ID"] ) )
						if( in_array( $id, $arProfile["IBLOCK_TYPE_ID"] ) )
							$selected = 'selected="selected"';?>
					<option value="<?=$id?>" <?=$selected?>><?=$type["NAME"]?></option>
				<?}?>
			</select>
		</p>
		<p id="iblock_select_block">
			<select multiple="multiple" name="PROFILE[IBLOCK_ID][]">
				<?foreach( $ibtypes as $type ){
                    if( is_array( $type["IBLOCK"] ) ){
                        foreach( $type["IBLOCK"] as $id => $iblock ){
                            $selected = "";
							if( is_array( $arProfile["IBLOCK_ID"] ) )
								if( in_array( $id, $arProfile["IBLOCK_ID"] ) )
									$selected = 'selected="selected"';?>
							
                            <option value="<?=$id?>" <?=$selected?>><?=$iblock?></option>
						<?}
                    }
                }?>
			</select>
		</p>
		<?$categories = $profileUtils->GetSections(
			$arProfile["IBLOCK_ID"],
			$arProfile["CHECK_INCLUDE"] == "Y"
		);?>
		<p id="section_select_block">
			<?if( !empty( $categories ) ){?>
                <select multiple="multiple" name="PROFILE[CATEGORY][]" class="category_select">
				    <?$sect = array();
					foreach( $categories as $depth )
						foreach( $depth as $id => $section )
							$sect[$id] = $section;
					
                    asort( $sect );
				    
                    foreach( $sect as $id => $section ){
                        $selected = "";
						if( is_array( $arProfile["CATEGORY"] ) )
							if( in_array( $id, $arProfile["CATEGORY"] ) )
								$selected = 'selected="selected"';?>
					    
                        <option value="<?=$id?>" <?=$selected?>><?=$section["NAME"]?></option>
				    <?}?>
			    </select>
            <?}?>
		</p>
	</td>
</tr>
