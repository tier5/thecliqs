<?php if ($this->pageCount > 1): ?>
<ul class="paginationControl">
    <?php /* Previous page link */ ?>
    <?php if (isset($this->previous)): ?>
        <li>
            <a href="javascript:void(0)" onclick="javascript:openPage(<?php echo $this->previous;?>)"><?php echo "<b>".$this->translate('&#171; Previous')."</b>";?></a>
        </li>
    <?php endif; ?>

    <?php foreach ($this->pagesInRange as $page): ?>
    <?php if ($page != $this->current): ?>
        <li>
            <a href="javascript:void(0)" onclick="javascript:openPage(<?php echo $page;?>)"><?php echo $page;?></a>
        </li>
    <?php else: ?>
        <li class="selected">
            <a href="javascript:void(0)"><?php echo $page; ?></a>
        </li>
    <?php endif; ?>
    <?php endforeach; ?>

    <?php /* Next page link */ ?>
    <?php if (isset($this->next)): ?>
        <li>
            <a href="javascript:void(0)" onclick="javascript:openPage(<?php echo $this->next;?>)"><?php echo "<b>".$this->translate('Next &#187;')."</b>";?></a>
        </li>
    <?php endif; ?>
</ul>
<?php endif; ?>