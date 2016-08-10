<?php
    $this->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper'); 
?>

<!-- Content -->
<?php if( $this->paginator->getTotalItemCount() > 0 ): 
$business = $this->business;?>
<ul class="ynbusinesspages_classified classifieds_browse">           
    <?php foreach ($this->paginator as $classified): 
    	$owner = Engine_Api::_() -> getDbTable('mappings', 'ynbusinesspages') -> getOwner($classified);?>
    <li>
        <div class='classifieds_browse_photo'>
            <?php echo $this->htmlLink($classified->getHref(), $this->itemPhoto($classified, 'thumb.normal')) ?>
        </div>
        <div class='classifieds_browse_info'>
            <div class='classifieds_browse_info_title'>
                <h3>
                <?php echo $this->htmlLink($classified->getHref(), $classified->getTitle()) ?>
                <?php if( $classified->closed ): ?>
                    <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Classified/externals/images/close.png'/>
                <?php endif;?>
                </h3>
            </div>
            <div class='classifieds_browse_info_date'>
                <?php echo $this->timestamp(strtotime($classified->creation_date)) ?>
                - <?php echo $this->translate('posted by');?> <?php echo $this->htmlLink($owner->getHref(), $owner->getTitle()) ?>
            </div>
            <div class='classifieds_browse_info_blurb'>
                <?php $fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($classified)?>
                <?php echo $this->fieldValueLoop($classified, $fieldStructure) ?>
            </div>
            <div class="classifieds_browse_body">
                <?php echo $this->string()->truncate($this->string()->stripTags($classified->body), 300) ?>
            </div>
        </div>
    </li>       
    <?php endforeach; ?>             
</ul>  
<?php endif; ?>