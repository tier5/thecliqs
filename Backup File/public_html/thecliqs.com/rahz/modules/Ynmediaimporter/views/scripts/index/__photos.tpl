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
<div class="ynimporter-photo-wrapper">
    <div>
        <div class="ynimporter-photo-thumb-wrapper" title="<?php echo htmlspecialchars(urldecode($item['title']));?>">
            <!-- <a class="ynimporter-album-thumb-stager" href="<?php echo $this->url(array('controller'=>'facebook','action'=>'index','media'=>'photo','extra'=>'aid','aid'=>$item['aid']),'ynmediaimporter_extended',1);?>"> -->
            <div class="ynimporter-photo-thumb-stager" >
                <i style="background-image: url(<?php echo $item['src_thumb']; ?>)"></i>
            </div>
        </div>
        <div class="ynimporter-fly-action">
            <!-- <ul>
                <li><a href="#">imported</a></li>
                <li><a href="#">imported</a></li>
                <li><a href="#">imported</a></li>
            </ul> -->
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
            ?> data-cache='<?php echo json_encode($item); ?>' type="checkbox" name="ynmediaimporter[]" value="<?php echo $item['id']; ?>" provider="<?php echo $item['provider']; ?>"  media="<?php echo $item['media']; ?>"  /><?php echo $this -> translate('import_status_' . $item['status']); ?></div>
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
        <?php echo $this->translate("No more photos found!");?>
    </div>
<?php endif; ?>