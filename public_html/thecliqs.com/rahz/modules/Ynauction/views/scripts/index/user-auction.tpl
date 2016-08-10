<div class="layout_page_ynauction_index_manageauction">
<div class="generic_layout_container layout_top">
  <div class="generic_layout_container layout_middle">
    <div class="generic_layout_container">
      <div class="headline">
        <h2>
          <?php echo $this->translate('Auction');?>
        </h2>
        <div class="tabs">
          <?php
            // Render the menu
            echo $this->navigation()
              ->menu()
              ->setContainer($this->navigation)
              ->render();
          ?>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="generic_layout_container layout_main">
  <div class="generic_layout_container layout_right">
    <div class="generic_layout_container">
        <?php echo $this->form->render($this) ?>
      </div>
      <script type="text/javascript">
        var pageAction =function(page){
          $('page').value = page;
          $('filter_form').submit();
        }
      </script>
    </div>
  </div>

  <div class='generic_layout_container layout_middle'>
    <div class="generic_layout_container">
      
    <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
      <ul class="ynauctions_browse">
        <?php foreach( $this->paginator as $item ): ?>
          <li>
            <div class='ynauctions_browse_photo'>
              <a href="<?php echo $item->getHref();?>"><?php echo $this->itemPhoto($item, 'thumb.normal') ?></a>
            </div>
            <div class='ynauctions_browse_info'>
              <p class='ynauctions_browse_info_title'>
                <?php echo $this->htmlLink($item->getHref(), $item->title) ?>
              </p>
              <p class='ynauctions_browse_info_date'>
                <?php echo $this->translate('Posted by');?>
                <?php echo $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle()) ?>
                <?php echo $this->translate('about');?>
                <?php echo $this->timestamp(strtotime($item->creation_date)) ?>
              </p>
              <p class='ynauctions_browse_info_blurb'>
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
     <?php else: ?>
      <div class="tip">
        <span>
          <?php echo $this->translate('There are no auctions yet.');?>
        </span>
      </div>
     <?php endif; ?>
     <?php echo $this->paginationControl($this->paginator, null, array("pagination/auctionpagination.tpl","ynauction"), array("orderby"=>$this->orderby)); ?>
    </div>
  </div>
</div>