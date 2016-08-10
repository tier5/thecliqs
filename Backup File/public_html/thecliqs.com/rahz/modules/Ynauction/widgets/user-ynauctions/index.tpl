 <div class="global_form_box" style="margin-bottom: 15px; overflow: auto;">   
 <ul class="ynauctions_browse">
  <?php foreach( $this->auctions as $item ): ?>
    <li>
    <div class='ynauctions_browse_photo'>
      <?php $owner = $item->getOwner();
       echo $this->htmlLink($owner->getHref(), $this->itemPhoto($owner, 'thumb.icon')) ?>
    </div>
     <div class='ynauctions_browse_info'>
        <div class='ynauctions_browse_title'>
          <?php echo $this->htmlLink($owner->getHref(), $owner->getTitle()) ?>
        </div>
        <div class='ynauctions_browse_date'>
          <?php echo $item->count; if($item->count <= 1): echo $this->translate(' auction'); else:  echo $this->translate(' auctions'); endif;?> 
        </div>
      </div>
    </li>
  <?php endforeach; ?>
</ul>
</div>

