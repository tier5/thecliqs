<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl  05.01.12 15:44 TeaJay $
 * @author     Taalay
 */
?>

<h2>
  <?php echo $this->translate('Credits Plugin') ?>
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
  <?php echo $this->translate("CREDIT_VIEWS_SCRIPTS_ADMINMEMBERS_INDEX_DESCRIPTION") ?>
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
    <?php echo $this->translate(array("%s member found", "%s members found", $this->locale()->toNumber($count)),
        $this->locale()->toNumber($count)) ?>
  </div>
  <div>
    <?php echo $this->paginationControl($this->paginator, null, null, array(
      'pageAsQuery' => true,
      'query' => $this->formValues
    )); ?>
  </div>
</div>

<br />

<div class="admin_table_form">
  <form id='multimodify_form' method="post" action="<?php echo $this->url(array('action'=>'multi-modify'));?>" onSubmit="multiModify()">
    <table class='admin_table'>
      <thead>
        <tr>
          <th style='width: 1%;'><a href="javascript:void(0);" onclick="javascript:changeOrder('user_id', 'DESC');"><?php echo $this->translate("ID") ?></a></th>
          <th><a href="javascript:void(0);" onclick="javascript:changeOrder('displayname', 'ASC');"><?php echo $this->translate("Display Name") ?></a></th>
          <th class="admin_table_centered"><a href="javascript:void(0);" onclick="javascript:changeOrder('current_credit', 'ASC');"><?php echo $this->translate("Current Credit") ?></a></th>
          <th class="admin_table_centered"><a href="javascript:void(0);" onclick="javascript:changeOrder('earned_credit', 'ASC');"><?php echo $this->translate("Earned Credit") ?></a></th>
          <th class="admin_table_centered"><a href="javascript:void(0);" onclick="javascript:changeOrder('spent_credit', 'ASC');"><?php echo $this->translate("Spent Credit") ?></a></th>
          <th style='width: 1%;' class='admin_table_options'><?php echo $this->translate("Options") ?></th>
        </tr>
      </thead>
      <tbody>
        <?php if( count($this->paginator) ): ?>
          <?php foreach( $this->paginator as $item ):
            $user = $this->item('user', $item->user_id);
            ?>
            <tr>
              <td><?php echo $this->locale()->toNumber($item->user_id) ?></td>
              <td class='admin_table_bold'>
                <?php echo $this->htmlLink($user->getHref(),
                    $this->string()->truncate($user->getTitle(), 10),
                    array('target' => '_blank'))?>
              </td>
              <td class="center"><?php echo $this->locale()->toNumber($item->current_credit) ?></td>
              <td class="center"><?php echo $this->locale()->toNumber($item->earned_credit) ?></td>
              <td class="center"><?php echo $this->locale()->toNumber($item->spent_credit) ?></td>

              <td class='admin_table_options center'>
                <a href='<?php echo $this->url(array('action' => 'edit', 'user_id' => $item->user_id), 'admin_members_credit', true);?>'>
                  <?php echo $this->translate("edit") ?>
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </form>
</div>