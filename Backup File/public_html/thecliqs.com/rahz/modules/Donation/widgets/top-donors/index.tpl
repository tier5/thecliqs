<?php
/**
 * Created by JetBrains PhpStorm.
 * User: adilet
 * Date: 21.08.12
 * Time: 12:31
 * To change this template use File | Settings | File Templates.
 */?>

<ul>
  <a style="font-weight: bold; float: right; padding-right: 5px;" href="<?php echo $this->url(array('controller' => 'donors', 'action' => 'index'), 'donation_extended', true);?>"><?php echo  $this->translate('View All') ?></a>
  <?php foreach( $this->paginator as $donation ): ?>
  <li>
    <?php echo $this->htmlLink($donation->getOwner()->getHref(), $this->itemPhoto($donation->getOwner(), 'thumb.icon'), array('class' => 'donors_thumb')) ?>
    <div class='donors_info'>
      <div class='donors_name'>
        <?php echo $this->htmlLink($donation->getOwner()->getHref(), $donation->getOwner()->getTitle()) ?>
      </div>
      <div class='donors_amount'>
        <?php echo $this->translate('Donated:')?>
        <?php echo $this->locale()->toCurrency((double)$donation->amounted, $this->currency) ?>
      </div>
    </div>
  </li>
  <?php endforeach; ?>
</ul>