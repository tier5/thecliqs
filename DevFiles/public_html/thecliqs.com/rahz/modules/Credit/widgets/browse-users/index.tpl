<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl  06.01.12 16:32 TeaJay $
 * @author     Taalay
 */
?>

<div class="credit_browsemembers_results" id="credit_browsemembers_results">
  <a id="credit_loader_browse" class="credit_loader_browse hidden"><?php echo $this->htmlImage($this->layout()->staticBaseUrl.'externals/smoothbox/loading.gif', ''); ?></a>
  <ul id="credit_browsemembers_ul">
    <?php foreach( $this->top_users as $item ): ?>
      <?php $user = $this->item('user', $item->balance_id); ?>
      <li>
        <div class="credit_race">
          <h2 style="font-size: <?php echo 35-4*strlen($item->place)?>px"><?php echo $this->locale()->toNumber($item->place); ?></h2>
        </div>
        <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon')) ?>
        <div class='credit_browsemembers_results_info'>
          <?php echo $this->htmlLink($user->getHref(), $user->getTitle()) ?>
          <div class="browsemembers_credits">
            <div class="current_credit_icon" title="<?php echo $this->translate('Current Credits')?>">
              <span style="font-weight: bold"><?php echo $this->locale()->toNumber($item->current_credit)?></span>
            </div>
            <div class="earned_credit_icon" title="<?php echo $this->translate('Earned Credits')?>">
              <span><?php echo $this->locale()->toNumber($item->earned_credit)?></span>
            </div>
            <div class="spent_credit_icon" title="<?php echo $this->translate('Spent Credits')?>">
              <span><?php echo $this->locale()->toNumber($item->spent_credit)?></span>
            </div>
          </div>
        </div>
      </li>
    <?php endforeach; ?>
  </ul>
</div>
<br />

<?php if ($this->top_users->getTotalItemCount() > 1) : ?>
  <?php echo $this->paginationControl($this->top_users, null, array("pagination/pagination.tpl","credit"), array('identity' => $this->identity, 'class' => '.layout_credit_browse_users')); ?>
<?php endif; ?>

<?php if (!$this->top_users->getTotalItemCount()) : ?>
  <div class="tip">
    <span>
      <?php echo $this->translate("CREDIT_No one has earned credits."); ?>
    </span>
  </div>
<?php endif; ?>