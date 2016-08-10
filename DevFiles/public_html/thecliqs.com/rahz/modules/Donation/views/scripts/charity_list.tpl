
<?php $counter = 1; ?>
<?php if($this->donations->getTotalItemCount() > 0): ?>
  <ul class="donations">
    <?php foreach($this->donations as $donation): ?>
      <?php if($counter == 3): ?>
      <li class="third">
      <?php $counter = 0; ?>
      <?php else: ?>
      <li>
      <?php endif; ?>
        <div class="photo">
          <?php echo $this->htmlLink($donation->getHref(),$this->itemPhoto($donation,'thumb.donation')); ?>
        </div>
        <div class="donation_info">
          <div class="donation_title">
            <h3><?php echo $this->htmlLink($donation, $this->string()->chunk($this->string()->truncate($donation->getTitle(), 25), 10)) ?></h3>
          </div>
          <div class="donation_options">
            <div class="raised">
              <label><?php echo $this->translate('DONATION_Raised:'); ?></label>
              <span><?php echo $this->locale()->toCurrency((double)$donation->raised_sum, $this->currency) ?></span>
            </div>
          </div>
          <button name="submit" type="submit"
                  onclick="window.open('<?php echo $this->url(array('object' => $donation->getType(),'object_id' => $donation->getIdentity()),'donation_donate',true); ?>')">
            <?php echo $this->translate('DONATION_Donate'); ?>
          </button>
        </div>
        <?php if($this->like_count[$donation->getIdentity()]):?>
          <div class="label">
            <?php
              echo $this->translate(array("%s supporter:", "%s supporters:", $this->like_count[$donation->getIdentity()]), $this->locale()->toNumber($this->like_count[$donation->getIdentity()]));
            ?>
          </div>
          <div class="supporters">
            <?php foreach($this->supporters[$donation->getIdentity()] as $supporter): ?>
              <?php
                if(null!=$item = Engine_Api::_()->getItem('user',$supporter['user_id']))
                {
                  echo $this->htmlLink($item->getHref(),$this->itemPhoto($item,'thumb.icon'));
                }
              ?>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </li>
      <?php $counter++; ?>
    <?php endforeach; ?>
  </ul>
  <?php
    if($this->donations->count() > 1){
      echo $this->paginationControl($this->donations, null, array("pagination.tpl","donation"), array(
        'page' => $this->subject,
        'type' => 'charity'
      ));
    }
  ?>
<?php else: ?>
<br/>
<div class="tip">
    <span>
      <?php echo $this->translate('DONATION_Nobody has created a donation yet.');?>
      <?php if($this->subject->getDonationPrivacy('charity')): ?>
        <?php
          echo $this->translate('DONATION_Be the first to %1$screate%2$s one!',
            '<a href="'.$this->url(array('action' => 'create','controller' => 'charity','page_id' => $this->subject->getIdentity()),
              'donation_extended',true).'">', '</a>');
        ?>
      <?php endif; ?>
    </span>
</div>
<?php endif; ?>