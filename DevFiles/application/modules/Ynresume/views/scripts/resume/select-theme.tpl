<form class="ynresume-select-theme" method="post" class="global_popup_form">
    <h3><?php echo $this->translate('Select theme')?></h3>
    <ul id="theme-list">
    <?php $themes = array('theme_1', 'theme_2', 'theme_3', 'theme_4');?>
    <?php foreach ($themes as $theme) : ?>
        <li class="theme_item">
            <div><img src="<?php echo Engine_Api::_()->ynresume()->getThemeIconLink($theme)?>" /></div>
            <div><input name="theme" type="radio" <?php if ($theme == $this->resume->theme) echo 'checked'?> value="<?php echo $theme?>"/></div>
        </li>
    <?php endforeach;?>
    </ul>
    <button type="submit"><?php echo $this->translate('Save')?></button>
    <?php echo ' '.$this->translate('or').' '?>
    <a href="javascript:void(0)" onclick="parent.Smoothbox.close();"><?php echo $this->translate('Cancel')?></a>
</form>
