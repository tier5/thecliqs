<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2011-08-19 17:07:11 mirlan $
 * @author     Mirlan
 */
?>

<script type="text/javascript">
  function openProduct(url)
  {
    location.href = url;
  }
</script>

<div class="footer-products">
  <div class="footer-products-nav">
    <?php echo $this->htmlLink(
      array('action'=>'products', 'route'=>'store_general'),
      $this->translate('More Products'),
      array('class'=>'footer-products-right'))
    ?>
  </div>

  <div class="footer-products-wrapper">
    <?php foreach($this->products as $key => $item): ?>
      <div class="footer-products-item">

        <div class="footer-products-item-title">
          <?php
            echo $this->htmlLink($item->getHref(), $this->string()->truncate($item->getTitle(), 17, '...'));
          ?>
        </div>
        <?php if($item->page_id != 0): ?>
          <div class="footer-products-item-store">
            <?php
                $store = Engine_Api::_()->getItem('page', $item->page_id);
              echo $this->htmlLink($store->getHref(), $store->getTitle());
            ?>
          </div>
        <?php endif; ?>
        <div class="footer-products-thumbs-nocaptions">
          <table onclick="openProduct('<?php echo $item->getHref()?>')">
            <tr valign="middle">
              <td valign="middle" height="160" width="140">
                <div id="footer-products-photo_<?php echo $key?>" class="center">
                  <?php echo $this->itemPhoto($item, 'thumb.normal', '', array('style' => 'border: none'))?>
                </div>
              </td>
            </tr>
          </table>
        </div>
        <div class="footer-products-item-description">
          <div class="footer-products-item-description-add-button">
            <?php echo $this->getPriceBlock($item);?>
          </div>
        </div>

      </div>
    <?php endforeach; ?>
  </div>
</div>