<?php if ($this->pageCount > 1): ?>
  <div class="paginationControl">
    <?php /* Previous page link */ ?>
    <?php if (isset($this->previous)): ?>
      <a href="javascript:void(0)" onclick="javascript:pageAction(<?php echo $this->previous;?>)"><?php echo $this->translate('&#171; Previous');?></a>
      <?php if (isset($this->previous)): ?>
      &nbsp;
      <?php endif; ?>
    <?php endif; ?>

    <?php foreach ($this->pagesInRange as $page): ?>
    <span style = "margin:4px;">
      <?php if ($page != $this->current): ?>
        <a href="javascript:void(0)" onclick="javascript:pageAction(<?php echo $page;?>)"><?php echo $page;?></a> 
      <?php else: ?>
        <?php echo $page; ?> 
      <?php endif; ?>
      </span>
    <?php endforeach; ?>

    <?php /* Next page link */ ?>
    <?php if (isset($this->next)): ?>
        <a href="javascript:void(0)" onclick="javascript:pageAction(<?php echo $this->next;?>)"><?php echo $this->translate('Next &#187;');?></a>
    <?php endif; ?>

  </div>
<?php endif; ?>
<style type="text/css">
 .paginationControl
 {
	padding:6px;
	text-align:center;
    border: none;
    float: none;
    font-weight: bold;
 }
</style>