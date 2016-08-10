<?php if($this->paginator->getTotalItemCount() > 0): ?>
  <?php $counter =1; ?>
  <ul class="fundraisers">
    <?php foreach($this->paginator as $donation): ?>
    <?php if($counter == 3): ?>
    <li class="third">
      <?php $counter = 0; ?>
    <?php else: ?>
    <li>
    <?php endif; ?>
      <div class="photo">
        <?php echo $this->htmlLink($donation->getHref(),$this->itemPhoto($donation->getOwner(),'thumb.icon')); ?>
      </div>
      <div class="donation_info">
        <h3><?php echo $this->htmlLink($donation->getHref(),$donation->getTitle()); ?></h3>
        <span class="fauthor">
          <?php echo $this->translate('DONATION_by '); ?>
          <?php echo $this->htmlLink($donation->getOwner()->getHref(),$donation->getOwner()->getTitle()); ?>
        </span>
        <span class="ffor">
          <?php echo $this->translate('DONATION_for '); ?>
          <?php  echo $this->htmlLink($donation->getParent()->getHref(),$donation->getParent()->getTitle()); ?>
        </span>
        <br/>
        <br/>
        <div class="donation_options">
          <div class="raised">
            <label><?php echo $this->translate('DONATION_Raised:'); ?></label>
            <span><?php echo $this->locale()->toCurrency((double)$donation->raised_sum, $this->currency) ?></span>
          </div>
          <?php if($donation->getTargetSum() > 0): ?>
            <div class="raised">
              <label><?php echo $this->translate('DONATION_Target:'); ?></label>
              <span><?php echo $this->locale()->toCurrency((double)$donation->target_sum, $this->currency) ?></span>
            </div>
          <?php endif; ?>
          <?php if (strtotime($donation->expiry_date)):?>
            <div class="raised">
              <label><?php echo $this->translate('DONATION_Limited:'); ?></label>
              <?php
                $left = Engine_Api::_()->getApi('core', 'donation')->datediff(new DateTime($donation->expiry_date), new DateTime(date("Y-m-d H:i:s")));
                $month = (int)$left->format('%m');
                $day = (int)$left->format('%d');
              ?>
              <span>
                <?php if($month > 0): ?>
                  <?php echo $this->translate(array("%s month", "%s months", $month), $month);?>
                <?php endif;?>
                <?php echo $this->translate(array("%s day left", "%s days left", $day), $day);?>
              </span>
            </div>
          <?php endif; ?>
        </div>
        <button name="submit" type="submit"
                onclick="window.open('<?php echo $this->url(array('object' => $donation->getType(),'object_id' => $donation->getIdentity()),'donation_donate',true); ?>')">
          <?php echo $this->translate('DONATION_Donate'); ?>
        </button>
        <br/>
      </div>
      <?php if($donation->getTargetSum() > 0): ?>
        <div class="progress_cont">
          <div class="progress">
            <?php $status =  round(100 * $donation->getRaised() / $donation->getTargetSum(), 0);?>
            <div class="bar" style="width: <?php echo $status > 100 ? 100 : $status;?>%"></div>
          </div>
          <span><?php echo $status > 100 ? 100 : $status;?>%</span>
          <br />
        </div>
      <?php endif; ?>
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
  <?php if( $this->paginator->count() > 1 ): ?>
  <br />
  <?php echo $this->paginationControl(
      $this->paginator, null, null, array(
      'pageAsQuery' => true,
      'query' => $this->searchParams
    )); ?>
  <?php endif; ?>
<?php else:?>
  <div class="tip">
    <span>
      <?php echo $this->translate('DONATION_Nobody has raised money for any donation yet.');?>
    </span>
  </div>
<?php endif; ?>