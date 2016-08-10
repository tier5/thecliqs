<?php echo $this->render('_page_options_menu.tpl'); ?>
<div class='layout_left' style="width: auto;">
  <?php echo $this->render('_page_stat_tabs.tpl'); ?>
</div>

<div class='layout_middle'>
  <div class="stat_content">
  	<?php echo $this->render('_statFilterForm.tpl'); ?>
    <div class="stat_filter_desc"><?php echo $this->translate("PAGE_VIEWS_DESC"); ?></div>
  	<div class="clr"></div>
  </div>

  <div class="clr"></div>

  <?php echo $this->render('_pageViewsStatChart.tpl'); ?>
</div>