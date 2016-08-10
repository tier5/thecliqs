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
<?php if($this->canCreate):
echo $this->htmlLink(array(
              'action' => 'create',
			'module' => 'ynauction',
			 'controller' => 'manage',
              'route' => 'admin_default',
            ), $this->translate('Post New Auction'), array(
              'class' => 'buttonlink item_icon_ynauction',
              'style' => 'background-image: url(application/modules/Ynauction/externals/images/add_auction.jpg);'
            )); endif; ?>
<br/>
<br/> 
 <?php echo $this->count." ".$this->translate('auction(s)');   ?>
 <br/>
<?php if( count($this->paginator) ): ?>
<script type="text/javascript">
  en4.core.runonce.add(function(){
      $$('th.admin_table_short input[type=checkbox]').addEvent('click',
       function(){ 
           var checkboxes = $$('td.minh input[type=checkbox]');
            checkboxes.each(function(item, index){ 
                item.checked =  $('check_all').checked; 
           });  
       })});

  var delectSelected =function(){
    var checkboxes = $$('td.minh input[type=checkbox]');
    var selecteditems = [];
    checkboxes.each(function(item, index){
      var checked = item.checked;
      var value = item.value;
      if (checked == true && value != 'on'){
        selecteditems.push(value);
      }
    });

    $('ids').value = selecteditems;
    $('delete_selected').submit();
  }
  function ynauction_good(product_id,checbox){
            
      checbox = document.getElementById('goodynauction_' + product_id);
      var status = 1;     
      if(checbox.checked == true) 
      {
          status = 1;
      }
      else
      {
        status = 0;
      }
        new Request.JSON({
          'format': 'json',
          'url' : '<?php echo $this->url(array('module' => 'ynauction', 'controller' => 'manage', 'action' => 'featured'), 'admin_default') ?>',
          'data' : {
            'format' : 'json',
            'product_id' : product_id,
            'good' : status
          }
        }).send();   
    }
  function ynauction_stop(product_id,checbox){
        
      checbox = $('stopynauction_' + product_id);
      var status = 1;     
      if(checbox.checked == true) 
      {
          status = 1;
      }
      else
      {
        status = 0;
      }
        new Request.JSON({
          'format': 'json',
          'url' : '<?php echo $this->url(array('module' => 'ynauction', 'controller' => 'manage', 'action' => 'stop'), 'admin_default') ?>',
          'data' : {
            'format' : 'json',
            'product_id' : product_id,
            'stop' : status
          }
        }).send();
    }
  function ynauction_dis(product_id,checbox){
        
      checbox = $('disynauction_' + product_id);
      var status = 1;     
      if(checbox.checked == true) 
      {
          status = 1;
      }
      else
      {
        status = 0;
      }
        new Request.JSON({
          'format': 'json',
          'url' : '<?php echo $this->url(array('module' => 'ynauction', 'controller' => 'manage', 'action' => 'dis'), 'admin_default') ?>',
          'data' : {
            'format' : 'json',
            'product_id' : product_id,
            'dis' : status
          }
        }).send();
    } 
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
  <div style="overflow: auto;">
<table class='admin_table'>
  <thead>
    <tr>
      <th class='admin_table_short'><input type='checkbox' id="check_all" class='checkbox' /></th>
      <th class='admin_table_short'><a href="javascript:void(0);" onclick="javascript:changeOrder('product_id', 'DESC');"><?php echo $this->translate("ID") ?></a></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('title', 'DESC');"><?php echo $this->translate("Title") ?></a></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('cat_title', 'DESC');"><?php echo $this->translate("Category") ?></a></th> 
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('username', 'DESC');"><?php echo $this->translate("Owner") ?></a></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('featured', 'DESC');"><?php echo $this->translate("Featured") ?></a></th>  
       <th><a href="javascript:void(0);" onclick="javascript:changeOrder('display_home', 'DESC');"><?php echo $this->translate("Published") ?></a></th>  
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('stop', 'DESC');"><?php echo $this->translate("Stopped") ?></a></th>
      <th><?php echo $this->translate("Status") ?></th> 
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('start_time', 'DESC');"><?php echo $this->translate("Start Time") ?></a></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('end_time', 'DESC');"><?php echo $this->translate("End Time") ?></a></th>
      <th><?php echo $this->translate("Options") ?></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($this->paginator as $item): ?>
      <tr>
        <td class="minh"><input type='checkbox' class='checkbox' value="<?php echo $item->product_id ?>"/></td>
        <td><?php echo $item->product_id ?></td>
        <td><?php echo $item->getTitle() ?></td>
         <td><?php $cat =  Engine_Api::_()->getItem('ynauction_category',$item->cat_id);
         if($cat):
         echo $cat->title;
         endif;
         ?></td> 
        <td><?php echo $this->user($item->user_id) ?></td>
        <td>
        <?php if($item->featured == 1): ?>
        <input type="checkbox" id='goodynauction_<?php echo $item->product_id; ?>'  onclick="ynauction_good(<?php echo $item->product_id; ?>,this)" checked />
      		<?php else: ?>
       		<input type="checkbox" id='goodynauction_<?php echo $item->product_id; ?>'  onclick="ynauction_good(<?php echo $item->product_id; ?>,this)" />
      		<?php endif; ?> </td> 
          <td>
        <?php if($item->display_home == 1): ?>
        <input type="checkbox" id='disynauction_<?php echo $item->product_id; ?>'  onclick="ynauction_dis(<?php echo $item->product_id; ?>,this)" checked />
              <?php else: ?>
               <input type="checkbox" id='disynauction_<?php echo $item->product_id; ?>'  onclick="ynauction_dis(<?php echo $item->product_id; ?>,this)" />
              <?php endif; ?> </td> 
          <td>
      	<?php if($item->stop == 1): ?>
        <input type="checkbox" id='stopynauction_<?php echo $item->product_id; ?>'  onclick="ynauction_stop(<?php echo $item->product_id; ?>,this)" checked />
      		<?php else: ?>
       		<input type="checkbox" id='stopynauction_<?php echo $item->product_id; ?>'  onclick="ynauction_stop(<?php echo $item->product_id; ?>,this)" />
      		<?php endif; ?> </td>  
            
         <td style="text-align: center;">
             <?php 
             if($item->status == 0 && $item->display_home == 0): ?>
             <?php echo $this->translate("Created") ?>
             <?php elseif($item->status == 0 && $item->display_home == 1 && $item->approved == 0): ?> 
             <?php echo $this->translate("Pending") ?>
             <?php elseif($item->status == 0 && $item->display_home == 1 && $item->start_time > date('Y-m-d H:i:s')): ?> 
             <?php echo $this->translate("Upcoming") ?>
             <?php elseif($item->status == 0 && $item->display_home == 1): ?>
             <?php echo $this->translate("Running") ?>
             <?php elseif($item->status == 1): ?>
             <?php echo $this->translate("Won") ?> 
              <?php elseif($item->status == 2): ?>
             <?php echo $this->translate("Paid") ?>
             <?php elseif($item->status == 3): ?>
             <?php echo $this->translate("Ended") ?>
             <?php endif; ?>
            </td> 
        <td><?php 
        echo $this->locale()->toDateTime($item->start_time); ?></td>
        <td><?php 
        echo $this->locale()->toDateTime($item->end_time); ?></td>
        <td>
          <?php echo $this->htmlLink($item->getHref(), $this->translate('view')) ?>
          |
          <?php echo $this->htmlLink(array(
              'action' => 'edit',
          	  'module' => 'ynauction',
          	  'controller' => 'manage',
              'auction' => $item->getIdentity(),
              'route' => 'admin_default',
              'reset' => true,
            ), $this->translate('edit'), array(
              'class' => '',
            )) ?>
          |
          <?php echo $this->htmlLink(array(
              'action' => 'delete',
              'module' => 'ynauction',
              'controller' => 'manage',
              'auction' => $item->getIdentity(),
              'route' => 'admin_default',
              'reset' => true,
            ), $this->translate('delete'), array(
              'class' => ' smoothbox ',
            )) ?>
            <br/>
        <?php if($item->approved != 0):?>
        <?php if($item->approved == 1){ 
        echo $this->translate('Approved'); ?> | 
        <?php echo $this->htmlLink(array(
                  'action' => 'deny',
                  'auction' => $item->getIdentity(),
                  'route' => 'ynauction_general',
                  'reset' => true,
                ), $this->translate('Deny'), array(
                  'class' => ' smoothbox ',
                ));
        }else {
        echo $this->htmlLink(array(
                  'action' => 'approve',
                  'auction' => $item->getIdentity(),
                  'route' => 'ynauction_general',
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
                  'action' => 'approve',
                  'auction' => $item->getIdentity(),
                  'route' => 'ynauction_general',
                  'reset' => true,
                ), $this->translate('approve'), array(
                  'class' => ' smoothbox ',
                )) ?>
                 | 
                <?php echo $this->htmlLink(array(
                  'action' => 'deny',
                  'auction' => $item->getIdentity(),
                  'route' => 'ynauction_general',
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
</div>  
<br />

<div class='buttons'>
  <button onclick="javascript:delectSelected();" type='submit'>
    <?php echo $this->translate("Delete Selected") ?>
  </button>
</div>

<form id='delete_selected' method='post' action='<?php echo $this->url(array('action' =>'deleteselected')) ?>'>
  <input type="hidden" id="ids" name="ids" value=""/>
</form>
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
      <?php echo $this->translate("There are no auctions yet.") ?>
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