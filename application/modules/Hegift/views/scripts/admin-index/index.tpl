<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: index.tpl  03.02.12 15:09 TeaJay $
 * @author     Taalay
 */
?>

<h2>
  <?php echo $this->translate('Virtual Gifts Plugin') ?>
</h2>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<p>
  <?php echo $this->translate("HEGIFT_VIEWS_SCRIPTS_ADMININDEX_INDEX_DESCRIPTION") ?>
</p>
<br />

<script type="text/javascript">
  var currentOrder = '<?php echo $this->order ?>';
  var currentOrderDirection = '<?php echo $this->order_direction ?>';
  var changeOrder = function(order, default_direction){
    // Just change direction
    if( order == currentOrder ) {
      $('order_direction').value = ( currentOrderDirection == 'ASC' ? 'DESC' : 'ASC' );
    } else {
      $('order').value = order;
      $('order_direction').value = default_direction;
    }
    $('filter_form').submit();
  }
</script>

<div class='admin_search'>
  <?php echo $this->formFilter->render($this) ?>
</div>

<br />


<div class='admin_results'>
  <div>
    <?php $count = $this->paginator->getTotalItemCount() ?>
    <?php echo $this->translate(array("HEGIFT_%s gift found", "HEGIFT_%s gifts found", $this->locale()->toNumber($count)),
        $this->locale()->toNumber($count)) ?>
  </div>
  <div>
    <?php echo $this->paginationControl($this->paginator, null, null, array(
      'pageAsQuery' => true,
      'query' => $this->formValues
    )); ?>
  </div>

  <?php echo $this->htmlLink(
 		$this->url(array('module'=>'hegift', 'action'=>'create'), 'admin_default', true),
 		$this->translate('HEGIFT_Create New Gift'),
 		array('class'=>'buttonlink hegift_icon_create')
 	); ?>
</div>

<br />

<div class="admin_table_form">
  <form id='gifts_list_form'>
    <table class='admin_table'>
      <thead>
        <tr>
          <th style='width: 1%;'><a href="javascript:void(0);" onclick="javascript:changeOrder('gift_id', 'DESC');"><?php echo $this->translate("ID") ?></a></th>
          <th class="admin_table_centered"><?php echo $this->translate("HEGIFT_Photo") ?></th>
          <th><a href="javascript:void(0);" onclick="javascript:changeOrder('title', 'ASC');"><?php echo $this->translate("HEGIFT_Gift Name") ?></a></th>
          <th><?php echo $this->translate("HEGIFT_Type"); ?></th>
          <th style='width: 1%;'><?php echo $this->translate("HEGIFT_Category") ?></th>
          <th class="admin_table_centered"><a href="javascript:void(0);" onclick="javascript:changeOrder('credits', 'ASC');"><?php echo $this->translate("HEGIFT_Cost") ?></a></th>
          <th class="admin_table_centered"><a href="javascript:void(0);" onclick="javascript:changeOrder('amount', 'ASC');"><?php echo $this->translate("HEGIFT_Amount") ?></a></th>
          <th style='width: 1%;'><a href="javascript:void(0);" onclick="javascript:changeOrder('creation_date', 'DESC');"><?php echo $this->translate("Creation Date") ?></a></th>
          <th class='admin_table_options admin_table_centered'><?php echo $this->translate("Options") ?></th>
        </tr>
      </thead>
      <tbody>
        <?php if( count($this->paginator) ): ?>
          <?php foreach( $this->paginator as $gift ): ?>
            <tr>
              <td><?php echo $this->locale()->toNumber($gift->gift_id) ?></td>
              <td class="center">
                <?php if ($gift->photo_id) : ?>
                  <?php echo $this->itemPhoto($gift, 'thumb.icon', '', array('class' => 'thumb_icon')); ?>
                <?php else : ?>
                  <?php echo $this->translate('HEGIFT_This is not a gift without photo')?>
                <?php endif; ?>
              </td>
              <td class='admin_table_bold' title="<?php echo $gift->getTitle()?>"><?php echo $this->string()->truncate($gift->getTitle(), 10)?></td>
              <td title="<?php echo $this->translate(ucfirst($gift->getTypeName()))?>"><?php echo $this->translate(ucfirst($gift->getTypeName()))?></td>
              <td title="<?php echo $this->translate($gift->getCategoryName())?>"><?php echo $this->string()->truncate($this->translate($gift->getCategoryName()), 15)?></td>
              <td class="center"><?php echo ($gift->credits) ? $this->locale()->toNumber($gift->credits) : $this->translate('HEGIFT_Free') ?></td>
              <td class="center"><?php echo ($gift->amount === null) ? $this->translate('HEGIFT_unlimit') : $this->locale()->toNumber($gift->amount) ?></td>
              <td class="nowrap">
                <?php echo $this->timestamp($gift->creation_date) ?>
              </td>
              <td class='center'>
                <?php
                  if ($gift->enabled) {
                    echo $this->htmlLink(
                      array(
                       'route' => 'admin_default',
                       'module' => 'hegift',
                       'action' => 'enable',
                       'gift_id' => $gift->getIdentity(),
                       'value' => 0
                      ),
                      '<img title="'.$this->translate('HEGIFT_Disable').'" class="hegift-icon" src="application/modules/Hegift/externals/images/approved.png">'
                    );
                  } else {
                    echo $this->htmlLink(
                      array(
                       'route' => 'admin_default',
                       'module' => 'hegift',
                       'action' => 'enable',
                       'gift_id' => $gift->getIdentity(),
                       'value' => 1
                      ),
                      '<img title="'.$this->translate('HEGIFT_Enable').'" class="hegift-icon" src="application/modules/Hegift/externals/images/disapproved.png">'
                    );
                  }
                ?>
                <?php
                  echo $this->htmlLink(
                    array(
                     'route' => 'admin_default',
                     'module' => 'hegift',
                     'action' => 'edit',
                     'gift_id' => $gift->getIdentity(),
                    ),
                    '<img title="'.$this->translate('HEGIFT_Edit').'" class="hegift-icon" src="application/modules/Hegift/externals/images/edit_gift.png">'
                  );
                ?>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </form>
</div>