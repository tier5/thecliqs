<?php if ($this->pageCount > 1): ?>
<?php
  if (Engine_Api::_()->core()->hasSubject('page')) {
    $subject = Engine_Api::_()->core()->getSubject('page');
    $href = $subject->getHref() . '/content/'.$this->type.'_donations/content_id/';
  } else {
    $href = $this->page->getHref() . '/content/'.$this->type.'_donations/content_id/';
  }
  ?>
<ul class="paginationControl">
  <?php if (isset($this->previous)): ?>
  <li>
    <a href="<?php echo $href . $this->previous;?>" onclick="donation.set_page(<?php echo $this->previous;?>);return false;"><?php echo $this->translate('&#171; Previous');?></a>
  </li>
  <?php endif; ?>

  <?php foreach ($this->pagesInRange as $page): ?>
  <li class="<?php if ($page == $this->current): ?>selected<?php endif; ?>" >
    <a onclick="donation.set_page(<?php echo $page;?>);return false;" href="<?php echo $href . $page; ?>"><?php echo $page; ?></a>
  </li>
  <?php endforeach; ?>

  <?php if (isset($this->next)): ?>
  <li>
    <a href="<?php echo $href . $this->next;?>" onclick="donation.set_page(<?php echo $this->next;?>);return false;"><?php echo $this->translate('Next &#187;');?></a>
  </li>
  <?php endif; ?>
</ul>
<?php endif; ?>