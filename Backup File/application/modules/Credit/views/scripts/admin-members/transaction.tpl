<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: transaction.tpl  11.01.12 16:50 TeaJay $
 * @author     Taalay
 */
?>
<h2>
  <?php echo $this->translate('Credits Plugin') ?>&nbsp;&raquo;
  &nbsp;<a href="<?php echo $this->url(array('module' => 'credit', 'controller' => 'members'),'admin_default', true) ?>"><?php echo $this->translate("Members") ?></a>&nbsp;&raquo;
  &nbsp;<a href="<?php echo $this->user->getHref() ?>"><?php echo $this->user->getTitle() ?></a>
</h2>
<br />
<?php if( count($this->custom_nav) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->custom_nav)->render()
    ?>
  </div>
<?php endif; ?>

<p>
  <?php echo $this->translate("CREDIT_VIEWS_SCRIPTS_ADMINMEMBERS_TRANSACTIONS_DESCRIPTION") ?>
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

  var changeActionTypes = function(value) {
    $('action_id').disabled = true;
    new Request.JSON({
      url: '<?php echo $this->url(array('module' => 'credit', 'controller' => 'index', 'action' => 'types'), 'admin_default', true)?>',
      data: {
        type: value,
        format: 'json'
      },
      onSuccess: function(data) {
        $('action_id').disabled = false;
        $('action_id').innerHTML = data.html;
      }
    }).send();
  }

</script>

<div class='admin_search'>
  <?php echo $this->formFilter->render($this) ?>
</div>

<br />

<div class='admin_results'>
  <div>
    <?php $count = $this->paginator->getTotalItemCount() ?>
    <?php echo $this->translate(array("%s action found", "%s actions found", $this->locale()->toNumber($count)),
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
<form id='multimodify_form' method="post" onSubmit="multiModify()">
  <table class='admin_table'>
    <thead>
      <tr>
        <th><a href="javascript:void(0);" onclick="javascript:changeOrder('log_id', 'DESC');"><?php echo $this->translate("ID") ?></a></th>
        <th><a href="javascript:void(0);" onclick="javascript:changeOrder('action_name', 'ASC');"><?php echo $this->translate("Action Type") ?></a></th>
        <th class="center"><a href="javascript:void(0);" onclick="javascript:changeOrder('credit', 'ASC');"><?php echo $this->translate("Credits") ?></a></th>
        <th class="center" style='width: 1%;'><a href="javascript:void(0);" onclick="javascript:changeOrder('creation_date', 'DESC');"><?php echo $this->translate("Action Date") ?></a></th>
        <th style='width: 1%;' class='admin_table_options'><?php echo $this->translate("Options") ?></th>
      </tr>
    </thead>
    <tbody>
      <?php if( count($this->paginator) ): ?>
        <?php foreach( $this->paginator as $item ): ?>
          <tr>
            <td><?php echo $this->locale()->toNumber($item->log_id) ?></td>
            <td>
              <?php
                if ($item->object_type == null) {
                  echo $this->translate($item->action_name, $item->body);
                } else {
                  if (!Engine_Api::_()->credit()->isModuleEnabled($item->action_module)) {
                    if ($item->body) {
                      echo $this->translate($item->action_name, $item->body, '<i style="color: red">'.$this->translate('Plugin Disabled').'</i>');
                    } else {
                      echo $this->translate($item->action_name, '<i style="color: red">'.$this->translate('Plugin Disabled').'</i>');
                    }
                  } else {
                    if (($object = $this->item($item->object_type, $item->object_id)) !== null) {
                      if ($item->object_type == 'answer') {
                        $uri = $object->getHref();
                        $href = $uri['uri'];
                      } else {
                        $href = $object->getHref();
                      }
                      if ($item->body) {
                        echo $this->translate($item->action_name, $item->body, $this->htmlLink($href, ($object->getTitle())?$object->getTitle():$this->translate('click here'), array('target' => '_blank')));
                      } else {
                        echo $this->translate($item->action_name, $this->htmlLink($href, ($object->getTitle())?$object->getTitle():$this->translate('click here'), array('target' => '_blank')));
                      }
                    } else {
                      if ($item->body) {
                        echo $this->translate($item->action_name, $item->body, '<i style="color: red">'.$this->translate('Deleted').'</i>');
                      }  else {
                        echo $this->translate($item->action_name, '<i style="color: red">'.$this->translate('Deleted').'</i>');
                      }
                    }
                  }
                }
              ?>
            </td>
            <td class="center" style="color: <?php echo ($item->credit > 0) ? 'green' : 'red'?>"><?php echo $this->locale()->toNumber($item->credit) ?></td>
            <td class="nowrap center">
              <?php echo $this->locale()->toDateTime($item->creation_date) ?>
            </td>
            <td class='admin_table_options center'>
              <a href='<?php echo $this->url(
                array(
                  'module' => 'credit',
                  'controller' => 'index',
                  'action' => 'delete',
                  'log_id' => $item->log_id
                ), 'admin_default', true);?>' class="smoothbox">
                <?php echo $this->translate("delete") ?>
              </a>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</form>
</div>