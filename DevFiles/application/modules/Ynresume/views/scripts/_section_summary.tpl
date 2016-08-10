<?php 
    $label = Engine_Api::_()->ynresume()->getSectionLabel($this->section);
    $viewer = Engine_Api::_()->user()->getViewer();
    $resume = $this->resume;
    $params = $this->params;
    $manage = ($resume->isOwner($viewer)) && (!isset($params['view']) || !$params['view']);
    $create = (isset($params['create'])) ? $params['create'] : false;
    $edit = (isset($params['edit'])) ? $params['edit'] : false;
    $hide = (isset($params['hide'])) ? $params['hide'] : false;
?>
<?php
$summary = $resume->summary;
if (empty($summary) && $manage) {
    $create = true;
}
?>
<?php if (!empty($summary) || (!$hide && ($create || $edit))) : ?>
<?php $label = Engine_Api::_()->ynresume()->getSectionLabel($this->section); ?>
    <h3 class="section-label">
        <span class="section-label-icon"><i class="<?php echo Engine_Api::_()->ynresume()->getSectionIconClass($this->section);?>"></i></span>
        <span><?php echo $label;?></span>
    </h3>
    
    <div class="ynresume_loading" style="display: none; text-align: center">
        <img src='application/modules/Ynresume/externals/images/loading.gif'/>
    </div>
    
    <div class="ynresume-section-content">
<?php if ($manage) : ?>
    <?php if (!$hide && ($create || $edit)) : ?>
    <div id="ynresume-section-form-summary" class="ynresume-section-form">
        <form rel="summary" class="section-form">
            <p class="error"></p>
            <?php if ($edit && isset($params['item_id'])) : ?>
            <input type="hidden" name="item_id" class="item_id" id="summary-1" value='1' />
            <?php endif; ?>
            <div id="summary-summary-wrapper" class="ynresume-form-wrapper">
                <textarea id="summary-summary" name="summary"/><?php if (!empty($summary)) echo $summary?></textarea>
                <p class="error"></p>
            </div>
            <script type="text/javascript">
            	window.addEvent('domready', function() {
            		tinymce.init({ mode: "exact", elements: "summary-summary", plugins: "table,fullscreen,media,preview,paste,code,image,textcolor", theme: "modern", menubar: false, statusbar: false, toolbar1: "undo,|,redo,|,removeformat,|,pastetext,|,code,|,media,|,image,|,link,|,fullscreen,|,preview", toolbar2: "fontselect,fontsizeselect,bold,italic,underline,strikethrough,forecolor,backcolor,|,alignleft,aligncenter,alignright,alignjustify,|,bullist,numlist,|,outdent,indent,blockquote", toolbar3: "", element_format: "html", height: "225px", convert_urls: false, language: "en", directionality: "ltr" });
            	});
            </script>
            <div class="ynresume-form-buttons">
                <button type="submit" id="submit-btn"><?php echo $this->translate('Save')?></button>
                <button type="button" class="ynresume-cancel-btn"><?php echo $this->translate('Cancel')?></button>
                <?php if ($edit && isset($params['item_id'])) : ?>
                <?php echo $this->translate(' or ')?>
                    <a href="javascript:void(0);" class="ynresume-remove-btn"><?php echo $this->translate('remove summary')?></a>
                <?php endif; ?>                
            </div>            
        </form>
    </div>
    <?php endif;?>
<?php endif;?>
<?php if (!empty($summary)) : ?>
<div id="ynresume-section-list-summary" class="ynresume-section-list">
    <ul id="summary-list" class="section-list">
	    <li class="section-item" id="summary-1">
	        <?php if ($manage) : ?>
                <a href="javascript:void(0);" class="edit-section-btn"><i class="fa fa-pencil"></i></a>
            <?php endif; ?>

            <div class="summary-summary ynresume-description"><?php echo $summary?></div>	        
	    </li>
    </ul>
</div>    
<?php endif; ?>
</div>
<?php endif;?>