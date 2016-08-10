<?php $this->headTranslate(array("There is no selected!"));?>
<?php foreach($this->items as $item): ?>
<div class="ynimporter-album-wrapper">
    <div>
        <div class="ynimporter-album-thumb-wrapper" title="<?php echo addslashes($item['title']);?>">
            <!-- <a class="ynimporter-album-thumb-stager" href="<?php echo $this->url(array('controller'=>'facebook','action'=>'index','media'=>'photo','extra'=>'aid','aid'=>$item['aid']),'ynmediaimporter_extended',1);?>"> -->
            <a class="ynimporter-album-thumb-stager" href="javascript:void(0)" onclick="YnMediaImporter.updatePage('<?php echo http_build_query(array('service'=>$item['provider'],'media'=>'photo','extra'=>'aid','aid'=>$item['aid']))?>')">
                <div>
                    <i style="background-image: url(<?php echo $item['src_thumb'];?>)"></i>
                </div>
            </a>
        </div>
        <div class="ynimporter-fly-action">
            <!-- <ul>
                <li><a href="#">imported</a></li>
                <li><a href="#">imported</a></li>
                <li><a href="#">imported</a></li>
            </ul> -->
            <?php if($this->userId):?>
            <div><input class="ynmediaimporter_checkbox" data-cache='<?php echo json_encode($item); ?>' type="checkbox" name="ynmediaimporter[]" value="<?php echo $item['id'];?>" provider="<?php echo $item['provider'];?>"  media="<?php echo $item['media'];?>"  /><?php echo $this->translate('import_status_'. $item['status']);?></div>
            <?php endif; ?>
        </div>
        <div class="ynimporter-album-info">
            <div><span><?php echo $this->translate("photo(s):");?></span><span><?php echo $item['photo_count'];?></span></div>                        
        </div>
    </div>
</div>
<?php endforeach; ?>
<div style="clear:both"></div>
<div class="ynmediaimporter-control-area">
    <div class="ynmediaimporter-control-action">
        <a href="javascript:void(0);" onclick="YnMediaImporter.importMedia();"><?php echo $this->translate("Import Selected"); ?></a>
         - <a href="javascript:void(0);" onclick="YnMediaImporter.selectAll();"><?php echo $this->translate("Select All"); ?></a>
         - <a href="javascript:void(0);" onclick="YnMediaImporter.unselectAll();"><?php echo $this->translate("Unselect All"); ?></a>
     </div>
     <div class="ynmediaimporter-control-paging"><a href="javascript:void(0)" onclick='YnMediaImporter.previousPage(<?php echo json_encode($this->requestParam)?>);'>previous page</a> - <a href="javascript:void(0)" onclick='YnMediaImporter.nextPage(<?php echo json_encode($this->requestParam)?>);'>next page</a></div>
</div>
