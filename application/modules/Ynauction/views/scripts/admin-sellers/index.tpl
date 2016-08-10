<h2><?php echo $this->translate("Auction Plugin") ?></h2>

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
  <?php echo $this->translate("YNAUCTION_VIEWS_SCRIPTS_ADMINMANAGE_INDEX_DESCRIPTION") ?>
</p>

<br /> 
<div class='admin_search'>   
<?php  echo $this->form->render($this); ?>
</div>
 
<br/> 
 <?php echo $this->count." ".$this->translate('seller(s)');   ?>
 <br/>
<?php if( count($this->paginator) ): ?>
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
<table class='admin_table'>
  <thead>
    <tr>
      <th width="5%" class='admin_table_short'><a href="javascript:void(0);" onclick="javascript:changeOrder('become_id', 'DESC');"><?php echo $this->translate("ID") ?></a></th>
      <th width="30%"><a href="javascript:void(0);" onclick="javascript:changeOrder('username', 'DESC');"><?php echo $this->translate("Name") ?></a></th>
      <th><?php echo $this->translate("Phone")?></th>
      <th><?php echo $this->translate("Address")?></th>
      <th><?php echo $this->translate("Options") ?></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($this->paginator as $item): 
    $user = Engine_Api::_()->getItem('user',$item->user_id);?>
      <tr>
        <td><?php echo $item->become_id; ?></td>
        <td><a href="<?php echo $user->getHref(); ?>">
        <?php echo $user ?></a></td>
        <td><?php echo $item->phone ?></td>
        <td><?php echo $item->address ?></td>
        <td>
            <?php if($item->approved != 0):?>
        <?php if($item->approved == 1){ 
        echo $this->translate('Approved'); ?> | 
        <?php echo $this->htmlLink(array(
                  'module' => 'ynauction',
                  'controller' => 'sellers',
                  'action' => 'deny',
                  'become_id' => $item->getIdentity(),
                  'route' => 'admin_default',
                  'reset' => true,
                ), $this->translate('Deny'), array(
                  'class' => ' smoothbox ',
                ));
        }else {
        echo $this->htmlLink(array(
                  'module' => 'ynauction',
                  'controller' => 'sellers',
                  'action' => 'approve',
                  'become_id' => $item->getIdentity(),
                  'route' => 'admin_default',
                  'reset' => true,
                ), $this->translate('Approve'), array(
                  'class' => ' smoothbox ',
                )); ?> | 
               <?php
                  echo $this->translate('Denied'); 
                  }
                 ?>
        <?php else: ?>    
            <?php echo $this->htmlLink(array(
                  'module' => 'ynauction',
                  'controller' => 'sellers',
                  'action' => 'approve',
                  'become_id' => $item->getIdentity(),
                  'route' => 'admin_default',
                  'reset' => true,
                ), $this->translate('approve'), array(
                  'class' => ' smoothbox ',
                )) ?>
                 | 
                <?php echo $this->htmlLink(array(
                  'module' => 'ynauction',
                  'controller' => 'sellers',
                  'action' => 'deny',
                  'become_id' => $item->getIdentity(),
                  'route' => 'admin_default',
                  'reset' => true,
                ), $this->translate('deny'), array(
                  'class' => ' smoothbox ',
                )) ?>
        <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<br/>
<div>
   <?php  echo $this->paginationControl($this->paginator, null, null, array(
      'pageAsQuery' => false,
      'query' => $this->formValues,
    ));     ?>
</div>

<?php else:?>
  <div class="tip">
    <span>
      <?php echo $this->translate("There are no sellers yet.") ?>
    </span>
  </div>
<?php endif; ?>
<style type="text/css">
.tabs > ul > li {
    display: block;
    float: left;
    margin: 2px;
    padding: 5px;
}
.tabs > ul {
 display: table;
  height: 65px;
}
</style>