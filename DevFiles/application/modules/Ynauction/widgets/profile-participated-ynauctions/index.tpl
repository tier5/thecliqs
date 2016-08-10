<ul class="ynauctions_browse"> 
  <?php foreach( $this->paginator as $item ): ?>
    <li style="padding-top:15px">
    <div class='ynauctions_browse_photo'>
      <?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.normal')) ?>
    </div>
     <div class='ynauctions_browse_info' style="width: 70%;">
        <div class='ynauctions_browse_title'>
        <span >
          <?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?>
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
        <p class='ynauctions_browse_info_blurb'>
        <?php $fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($item)?>
              <?php echo $this->fieldValueLoop($item, $fieldStructure) ?>
              <?php
                // Not mbstring compat
                echo substr(strip_tags($item->description), 0, 350); if (strlen($item->description)>349) echo "...";
              ?>
            </p>
        <div>
        <br/>
        <?php for($i = 1; $i <= 5; $i++): ?>
                <img border="0" src="application/modules/Ynauction/externals/images/<?php if ($i <= $item->rates): ?>star_full.png<?php elseif( $i > $item->rates &&  ($i-1) <  $item->rates): ?>star_part.png<?php else: ?>star_none.png<?php endif; ?>" width="16px" />
         <?php endfor; ?>
        </div>
      </div>
    </li>
  <?php endforeach; ?>
</ul>
<?php
  // show view all
  if( $this->paginator->count() > 0 ):
?>
  <?php echo $this->htmlLink($this->url(array('action'=>'participate','user_id' => Engine_Api::_()->core()->getSubject()->getIdentity()), 'ynauction_general'), $this->translate('View All Auctions'), array('class' => 'buttonlink icon_ynauction_viewall')) ?>
<?php endif;?>