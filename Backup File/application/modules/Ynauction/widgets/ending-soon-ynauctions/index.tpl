 <div class="global_form_box" style="margin-bottom: 15px;">
 <ul class="ynauctions_browse"> 
  <?php foreach( $this->data as $item ): ?>
    <li>
    <div class='ynauctions_browse_photo'>
      <?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.icon')) ?>
    
    </div>
     <div class='ynauctions_browse_info'>
        <div class='ynauctions_browse_title'>
        <span title="<?php echo $item->getTitle() ?>">
         <?php echo $this->htmlLink($item->getHref(), substr($item->getTitle(), 0, 35)) ?>
                <?php if(strlen($item->getTitle()) > 35) echo $this->translate('...');?>
          </span>
        </div>
        <div class='ynauctions_browse_date'>
          <?php
            $owner = $item->getOwner();
            echo $this->translate('Posted by %1$s', $this->htmlLink($owner->getHref(), $owner->getTitle()));
          ?>
        </div>
        <div class='ynauctions_browse_date'>
          <?php echo $this->timestamp($item->creation_date) ?>
        </div>
        <div>
        <?php for($i = 1; $i <= 5; $i++): ?>
                <img border="0" src="application/modules/Ynauction/externals/images/<?php if ($i <= $item->rates): ?>star_full.png<?php elseif( $i > $item->rates &&  ($i-1) <  $item->rates): ?>star_part.png<?php else: ?>star_none.png<?php endif; ?>" width="16px" />
         <?php endfor; ?>
        </div>
      </div>
    </li>
  <?php endforeach; ?>
</ul>
</div>   
 
