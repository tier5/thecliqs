<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: index.tpl  01.03.12 11:17 TeaJay $
 * @author     Taalay
 */
?>

<?php
	$this->headScript()
    ->appendFile( $this->layout()->staticBaseUrl . 'application/modules/Hegift/externals/scripts/core.js')
  ;
?>

<script type="text/javascript">
  gift_manager.send_url = '<?php echo $this->url(array('action' => 'send'), 'hegift_general', true) ?>';
</script>

<div class="headline">
  <h2>
    <?php echo $this->translate('Virtual Gifts');?>
  </h2>
  <?php if( count($this->navigation) > 0 ): ?>
    <div class="tabs">
      <?php
        // Render the menu
        echo $this->navigation()
          ->menu()
          ->setContainer($this->navigation)
          ->render();
      ?>
    </div>
  <?php endif; ?>
</div>

<p class="gift_description"><?php echo $this->translate('HEGIFT_Temporary Gifts Description')?></p>
<br />

<div class='layout_middle'>
  <ul class="gift_manage">
    <?php foreach( $this->paginator as $gift ):
      $user = $this->item('user', $gift->owner_id);
    ?>
      <li>
        <div class='gift_manage_photo'>
          <img src="<?php echo $gift->getPhotoUrl('thumb.icon') ?>" />
        </div>
        <div class="gift_manage_options">
          <ul>
            <li>
              <?php echo $this->htmlLink(
                $this->url(
                  array(
                    'action' => $gift->getTypeName(),
                    'gift_id' => $gift->getIdentity()
                  ), 'hegift_own', true
                ), $this->translate('HEGIFT_View Gift'), array('class' => 'buttonlink icon_gifts_view smoothbox')
              )?>
            </li>

            <?php if (!$gift->getStatus()) : ?>
              <li>
                <a class="buttonlink item_icon_gift"
                   onclick="gift_manager.open_form(<?php echo $gift->getIdentity()?>)"
                   href="javascript:void(0)"><?php echo $this->translate('HEGIFT_Send Gift');?>
                </a>
              </li>

              <li>
                <?php
                  echo $this->htmlLink(
                    $this->url(
                      array(
                        'action' => 'delete',
                        'gift_id' => $gift->getIdentity()
                      ), 'hegift_temp', true
                    ), $this->translate('HEGIFT_Delete Gift'), array('class' => 'buttonlink icon_gifts_decline smoothbox')
                )?>
              </li>
            <?php endif; ?>
          </ul>
        </div>
        <div class="gift_manage_info">
          <div class="gift_manage_info_title" style="clear: both">
            <h3><?php echo $gift->getTitle()?></h3>
          </div>
          <div class='gift_manage_info_date'>
            <?php echo $this->translate('HEGIFT_created %s ', $this->timestamp($gift->creation_date))?><br />
          </div>
          <span class='gift_manage_info_date' style="color: red">
            <?php echo $this->translate('HEGIFT_will be removed %s', $this->timestamp($gift->getRemovingDate()))?>
          </span>
        </div>
      </li>
    <?php endforeach; ?>
  </ul>

  <?php echo $this->paginationControl($this->paginator, null, null, array()); ?>
</div>