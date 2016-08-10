<?php if ($this->paginator->getTotalItemCount() > 0): ?>

<?php foreach ($this->paginator as $friend):?>
    <li>
        <input type="checkbox" name="users[]" id="users-<?php echo $friend->getIdentity()?>" value="<?php echo  $friend->getIdentity()?>">
        <label for="users-<?php echo $friend->getIdentity()?>"><?php echo $this->htmlLink($friend->getHref(), $friend->getTitle(), array('target' => '_blank'))?></label>
    </li>
    <?php endforeach;?>
    <?php if ($this->paginator->getCurrentPageNumber() < $this->paginator->getPages()->pageCount): ?>
        <div id="load_more_container">
            <button type='button' id='viewmore'>
                <?php echo $this->translate("View More") ?>
            </button>
        </div>
    <?php endif;?>
<?php else: ?>
<li>
    <?php echo $this->translate('You have no friends you can invite.'); ?>
</li>
<?php endif;?>


