<?php if ($this->selectTheme) : ?>
<form id="ynresume-select-theme" method="post">
    <p><?php echo $this->translate('Please select resume theme which match your need')?></p>
    <ul id="theme-list">
    <?php $themes = array('theme_1', 'theme_2', 'theme_3', 'theme_4');?>
    <?php foreach ($themes as $theme) : ?>
        <li class="theme_item">
            <div><img src="<?php echo Engine_Api::_()->ynresume()->getThemeIconLink($theme)?>" /></div>
            <div><input name="theme" type="radio" <?php if ($theme == 'theme_1') echo 'checked'?> value="<?php echo $theme?>"/></div>
        </li>
    <?php endforeach;?>
    </ul>
    <button type="submit"><?php echo $this->translate('Continue')?></button>
</form>
<?php endif; ?>