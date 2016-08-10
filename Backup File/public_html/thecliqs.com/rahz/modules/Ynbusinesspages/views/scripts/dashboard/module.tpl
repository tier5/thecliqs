<h3 class="ynbusinesspages-manage-modules-title"><?php echo $this->translate('Manage Modules')?></h3>
<p class="ynbusinesspages-manage-module-rescription"><?php echo $this->translate('Here is place you can manage all your modules.')?></p>
<div class="modules-list-div">
    <?php if (empty($this->modules)) :?>
    <div class="tip">
        <span><?php echo $this->translate('No modules available for this business.')?></span>
    </div>
    <?php else: ?>
    <ul class="modules-list">
        <?php foreach ($this->modules as $module) : ?>
        <?php if (!Engine_Api::_() -> hasItemType($module->item_type)) continue; ?>
        <li class="module-item" id="module_<?php echo $module->item_type?>">
            <div class="module-photo">
                <img src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Ynbusinesspages/externals/images/icon-<?php echo $module->item_type ?>.png" />
            </div>
            <div class="module-title">
            <?php echo $this -> translate($module->title)?>
            </div>
            <div class="module-options">
            <?php $options = $module->getOptions($this->business->getIdentity());?>  
            <?php foreach ($options as $option) :?>
            <span class="module-option-item"><?php echo $this->htmlLink($option['url'], $option['title'] , array ('class' => (isset($option['class'])) ? $option['class'].' module-link' : 'module-link'))?></span>
            <?php endforeach;?>  
            </div>
        </li>
        <?php endforeach;?>
    </ul>
    <?php endif; ?>
</div>
