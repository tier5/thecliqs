<?php $this->headTranslate(array("There is no selected!"));?>
<?php if($this->item_count>0):?>
<?php if(!isset($this->params['noControl']) or !$this->params['noControl']):?>
<div class="ynmediaimporter-control-area">
    <div class="ynmediaimporter-control-action">
        <?php if($this->userId):?>
        <a href="javascript:void(0);" onclick="YnMediaImporter.importMedia();" class="buttonlink ynmediaimporter_link_import"><?php echo $this -> translate("Import Selected"); ?></a>
        <a href="javascript:void(0);" onclick="YnMediaImporter.refresh(1);" class="buttonlink ynmediaimporter_link_import"><?php echo $this -> translate("Refresh"); ?></a>
        <a href="javascript:void(0);" onclick="YnMediaImporter.selectAll();" class="buttonlink ynmediaimporter_link_selectall"><?php echo $this -> translate("Select All"); ?></a>
        <a href="javascript:void(0);" onclick="YnMediaImporter.unselectAll();" class="buttonlink ynmediaimporter_link_unselectall"><?php echo $this -> translate("Unselect All"); ?></a>
        <?php endif; ?> 
     </div>
     <div style="clear:both;"></div>
</div>

<?php endif; ?>
<?php foreach($this->items as $item): 
    $mediaParent = isset($item['media_parent'])?$item['media_parent']:'';
    $status = $item['status'];  
?>
<div class="ynimporter-album-wrapper">
    <div>
        <div class="ynimporter-album-thumb-wrapper" title="<?php echo urldecode($item['title']);?>">
            <a href="javascript:void(0)" onclick="YnMediaImporter.updatePage('<?php echo http_build_query(array('service'=>$item['provider'],'media'=>'photo','media_parent'=>$mediaParent,'extra'=>'aid','aid'=>$item['aid']))?>')">
                <div class="ynimporter-album-thumb-stager" >
                    <i style="background-image: url(<?php echo $item['src_thumb']; ?>)"></i>
                </div>
            </a>
        </div>
        
        <div class="ynimporter-album-title" title="<?php echo urldecode($item['title']);?>">
        	<a href="javascript:void(0)" onclick="YnMediaImporter.updatePage('<?php echo http_build_query(array('service'=>$item['provider'],'media'=>'photo','media_parent'=>$mediaParent,'extra'=>'aid','aid'=>$item['aid']))?>')">
                <strong><?php echo $this->string()->truncate(urldecode($item['title']), 20);?></strong>
            </a>
        </div>
        <div class="ynimporter-album-info">
            <div><span><?php echo $this -> translate(array("%s photo","%s photos", $item['photo_count']), $item['photo_count']); ?></span></div>                        
        </div>
        <div class="ynimporter-fly-action">
            <?php if($this->userId):?>
            <div><input <?php
                if ($status)
                {
                    echo 'class="ynmediaimporter_imported" disabled="disabled"';
                }
                else
                {
                    echo 'class="ynmediaimporter_checkbox"';
                };
            ?> data-cache='<?php echo Zend_Json::encode($item); ?>' type="checkbox" name="ynmediaimporter[]" value="<?php echo $item['id']; ?>" provider="<?php echo $item['provider']; ?>"  media="<?php echo $item['media']; ?>"  /><?php echo $this -> translate('import_status_' . $item['status']); ?></div>
            <?php endif; ?>
        </div>
        
    </div>
</div>
<?php endforeach; ?>
<div class="ynmeidaimporter_result_holder">
        <div style="clear:both"></div>
        <?php if($this->params['limit'] == $this->item_count):?>
        <div class="feed_viewmore" id="feed_viewmore">
            <a onclick='YnMediaImporter.viewMore(<?php echo json_encode($this->params)?>);' href="javascript:void(0);" id="feed_viewmore_link" class="buttonlink icon_viewmore"><?php echo $this->translate('View More');?></a>
        </div>
     <?php endif; ?>
</div>
<?php else: ?>
    <div class="ynmeidaimporter_result_holder">
        <div style="clear:both"></div>
    <?php echo $this->translate("No more albums found!");?>
    </div>
<?php endif; ?>