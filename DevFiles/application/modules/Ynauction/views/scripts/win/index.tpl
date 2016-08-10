 <?php  $this->locale()->setLocale("en_US"); ?>
<?php
function selfURL() {
     $server_array = explode("/", $_SERVER['PHP_SELF']);
      $server_array_mod = array_pop($server_array);
      if($server_array[count($server_array)-1] == "admin") { $server_array_mod = array_pop($server_array); }
      $server_info = implode("/", $server_array);
      return "http://".$_SERVER['HTTP_HOST'].$server_info."/";
 } 
       ?>
    
  <?php if( $this->wins->getTotalItemCount() > 0 ): ?>
    <ul class="ynauctions_browse">
      <?php foreach( $this->wins as $item ): ?>
        <li>
          <div class='ynauctions_browse_photo'>
            <a href="<?php echo $item->getHref();?>"><?php echo $this->itemPhoto($item, 'thumb.icon') ?></a>
          </div>        
          <div class='ynauctions_browse_options'> 
          <div style="text-align: right;">
              <?php if($item->status == 1 || $item->status == 3): ?> 
                <?php echo $this->htmlLink(array(
                  'action' => 'checkout',
                  'auction' => $item->getIdentity(),
                  'route' => 'ynauction_winning',
                  'reset' => true,
                  'session_id' => session_id(),
                ), '', array(
                  'class' => 'buttonlink icon_ynauction_checkout',
                  'title' => $this->translate('Pay'), 
                  'style' => 'padding-left: 50px;padding-bottom: 20px;padding-top: 23px;'
                )) ?> 
                <?php elseif($item->status == 2): ?> 
                <?php echo $this->htmlLink(array(), '', array(
                  'class' => 'buttonlink icon_ynauction_paid',
                  'title' => $this->translate('refresh'),
                  'style' => 'padding-left: 50px;padding-bottom: 20px;padding-top: 23px;'
                )) ?> 
                <?php endif; ?> 
            </div>
                 <div style="color: Red; font-weight: bold;" title=" <?php echo $this->translate('Bid Price');?>">
                  <?php echo $this->locale()->toCurrency($item->bid_price,$item->currency_symbol); ?>
                  </div>  
          </div>   
          <div class='ynauctions_browse_info' style="width: 80%;">
            <p class='ynauctions_browse_info_title'>
              <?php echo $this->htmlLink($item->getHref(), $item->title) ?>
            </p>
            <p class='ynauctions_browse_info_date'>
              <?php echo $this->translate('Posted by');?>
              <?php echo $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle()) ?>
              <?php echo $this->translate('about');?>
              <?php echo $this->timestamp(strtotime($item->creation_date)) ?>
                       
            </p>
            <p class='ynauctions_browse_info_blurb' style="margin-bottom: 15px;">
            <?php $fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($item)?>
              <?php echo $this->fieldValueLoop($item, $fieldStructure) ?>
              <?php
                // Not mbstring compat
                echo substr(strip_tags($item->description), 0, 350); if (strlen($item->description)>349) echo "...";
              ?>
            </p>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php elseif($this->search): ?>
    <div class="tip">
      <span>
        <?php echo $this->translate('You do not have any auctions that match your search criteria.');?>
      </span>
    </div>
  <?php else: ?>
    <div class="tip">
      <span>
        <?php echo $this->translate('You do not have any winning auctions.');?>
      </span>
    </div>
  <?php endif; ?>
   <?php echo $this->paginationControl($this->wins,null, array("pagination/auctionpagination.tpl","ynauction"), array("orderby"=>$this->orderby));?>

