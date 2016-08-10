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

<div class="side-products">
  <div class="side-products-nav">
    <?php echo $this->htmlLink(
      array('module'=>'store', 'controller'=>'index', 'action'=>'products', 'route'=>'default'),
      $this->translate('More Products'),
      array('class'=>'side-products-right'))
    ?>
  </div>

  <div class="side-products-wrapper">
    <?php $cnt=0; foreach($this->products as $item): ?>
      <div class="side-products-item">

        <div class="side-products-item-title">
          <?php
            echo $this->htmlLink($item->getHref(), $this->string()->truncate($item->getTitle(), 17, '...'));
          ?>
        </div>
        <?php if($item->page_id != 0): ?>
          <div class="side-products-item-store">
            <?php
                $store = Engine_Api::_()->getItem('page', $item->page_id);
              echo $this->htmlLink($store->getHref(), $store->getTitle());
            ?>
          </div>
        <?php endif; ?>
        <div class="side-products-thumbs-nocaptions">
          <table onclick="openProduct('<?php echo $item->getHref()?>')">
            <tr valign="middle">
              <td valign="middle" height="160" width="140">
                <div id="side-products-photo_<?php echo $cnt; $cnt++;  ?>" class="center;">
                  <img src="<?php echo (null !== ($ico_tmp = $item->getPhotoUrl('thumb.normal'))) ? $ico_tmp : 'application/modules/Store/externals/images/nophoto_product_thumb_normal.png'; ?>" class="thumbs">
                </div>
              </td>
            </tr>
          </table>
        </div>
        <div class="side-products-item-description">
          <div class="side-products-item-description-add-button">
            <?php echo $this->getPriceBlock($item); ?>
          </div>
        </div>

      </div>
    <?php endforeach; $cnt=0;?>
  </div>
</div>