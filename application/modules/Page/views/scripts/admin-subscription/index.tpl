<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2011-08-11 17:53 taalay $
 * @author     Taalay
 */
?>

<h2>
  <?php echo $this->translate("Manage Subscriptions") ?>
</h2>
<?php if( count($this->navigation) ): ?>
  <div class='page_admin_tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render();
    ?>
  </div>
<?php endif; ?>
<p>
  <?php echo $this->translate("PAGE_VIEWS_ADMIN_SUBSCRIPTION_INDEX_DESCRIPTION") ?>
</p>

<br />


<?php if( !empty($this->error) ): ?>
  <ul class="form-errors">
    <li>
      <?php echo $this->error ?>
    </li>
  </ul>

  <br />
<?php return; endif; ?>


<script type="text/javascript">
  var currentOrder = '<?php echo $this->filterValues['order'] ?>';
  var currentOrderDirection = '<?php echo $this->filterValues['direction'] ?>';
  var changeOrder = function(order, default_direction){
    // Just change direction
    if( order == currentOrder ) {
      $('direction').value = ( currentOrderDirection == 'ASC' ? 'DESC' : 'ASC' );
    } else {
      $('order').value = order;
      $('direction').value = default_direction;
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
    <?php echo $this->translate(array("%s subscription found", "%s subscriptions found", $count), $count) ?>
  </div>
  <div>
    <?php echo $this->paginationControl($this->paginator, null, null, array(
      'query' => $this->filterValues,
      'pageAsQuery' => true,
    )); ?>
  </div>
</div>

<br />


<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
  <table class='admin_table'>
    <thead>
      <tr>
        <?php $class = ( $this->order == 'subscription_id' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->direction) : '' ) ?>
        <th class="admin_table_short <?php echo $class ?>">
          <a href="javascript:void(0);" onclick="javascript:changeOrder('subscription_id', 'DESC');">
            <?php echo $this->translate("ID") ?>
          </a>
        </th>
        <?php $class = ( $this->order == 'page_id' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->direction) : '' ) ?>
        <th class="<?php echo $class ?>">
          <a href="javascript:void(0);" onclick="javascript:changeOrder('page_id', 'ASC');">
            <?php echo $this->translate("Pages") ?>
          </a>
        </th>
        <?php $class = ( $this->order == 'package_id' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->direction) : '' ) ?>
        <th class='<?php echo $class ?>'>
          <a href="javascript:void(0);" onclick="javascript:changeOrder('package_id', 'ASC');">
            <?php echo $this->translate("Package") ?>
          </a>
        </th>
        <?php $class = ( $this->order == 'status' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->direction) : '' ) ?>
        <th class='center <?php echo $class ?>'>
          <a href="javascript:void(0);" onclick="javascript:changeOrder('status', 'ASC');">
            <?php echo $this->translate("Status") ?>
          </a>
        </th>
        <?php $class = ( $this->order == 'active' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->direction) : '' ) ?>
        <th class='center <?php echo $class ?>'>
          <a href="javascript:void(0);" onclick="javascript:changeOrder('active', 'DESC');">
            <?php echo $this->translate("Active") ?>
          </a>
        </th>
        <th class='center admin_table_options'>
          <?php echo $this->translate("Options") ?>
        </th>
      </tr>
    </thead>
    <tbody>
      <?php foreach( $this->paginator as $item ):
        $page = @$this->pages[$item->page_id];
        $package = @$this->packages[$item->package_id];
        ?>
        <tr>
          <td class="center"><?php echo $item->subscription_id ?></td>
          <td class='admin_table_bold'>
            <?php echo ( $page ? $page->__toString() : '<i>' . $this->translate('Deleted Page') . '</i>' ) ?>
          </td>
          <td class='admin_table_centered'>
            <?php if( $package ): ?>
              <a href='<?php echo $this->url(array('module' => 'page', 'controller' => 'packages', 'action' => 'edit', 'package_id' => $package->package_id)) ?>'>
                <?php echo $this->translate($package->name) ?>
              </a>
            <?php else: ?>
              <i><?php echo $this->translate('Missing Package') ?></i>
            <?php endif ?>
          </td>
          <td class="center"><?php echo $this->translate(ucfirst($item->status)) ?></td>
          <td class='center'>
            <?php echo ( $item->active ? $this->translate('Yes') : $this->translate('No') ) ?>
          </td>
          <td class='admin_table_options center'>
            <a class="smoothbox" href='<?php echo $this->url(array('action' => 'edit', 'subscription_id' => $item->subscription_id));?>'>
              <?php echo $this->translate("edit") ?>
            </a>
            |
            <a href='<?php echo $this->url(array('action' => 'detail', 'subscription_id' => $item->subscription_id));?>'>
              <?php echo $this->translate("details") ?>
            </a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
<?php endif; ?>