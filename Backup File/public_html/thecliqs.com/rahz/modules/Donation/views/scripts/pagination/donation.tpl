<?php if ($this->pageCount > 1): ?>
<ul class="paginationControl">
  <?php if (isset($this->previous)): ?>
  <li>
    <a href="javascript:void(0)" onclick="donation.set_page(<?php echo $this->previous;?>)"><?php echo $this->translate('&#171; Previous');?></a>
  </li>
  <?php endif; ?>

  <?php foreach ($this->pagesInRange as $page): ?>
  <li class="<?php if ($page == $this->current): ?>selected<?php endif; ?>" >
    <a onclick="donation.set_page(<?php echo $page;?>)" href="javascript:void(0)"><?php echo $page; ?></a>
  </li>
  <?php endforeach; ?>

  <?php if (isset($this->next)): ?>
  <li>
    <a href="javascript:void(0)" onclick="donation.set_page(<?php echo $this->next;?>)"><?php echo $this->translate('Next &#187;');?></a>
  </li>
  <?php endif; ?>
</ul>
<?php endif; ?>