<style type="text/css">
	#filter_form{
		background: none;
		padding: 0;
	}
</style>
<script type="text/javascript">

  en4.core.runonce.add(function(){
        $$('th.admin_table_short input[type=checkbox]').addEvent('click', function(){
        var checked = $(this).checked;
        var checkboxes = $$('input[type=checkbox]');
        checkboxes.each(function(item,index){
          item.checked = checked;
        });
      });
  });

  var changeOrder =function(orderby, direction){
    $('orderby').value = orderby;
    $('orderby_direction').value = direction;
    $('filter_form').submit();
  }

  var delectSelected =function(){
    var checkboxes = $$('input[type=checkbox]');
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

</script>
<?php $session = new Zend_Session_Namespace('mobile'); ?>
<h3><?php echo $this->translate('Manage Announcements') ?></h3>

 <?php echo $this->formFilter->render($this) ?>
<br />
<div class="business_discussions_options">
  <?php echo $this->htmlLink(array('route' => 'ynbusinesspages_profile', 'id' => $this->business->getIdentity()), $this->translate('Back to Business'), array(
    'class' => 'buttonlink icon_back'
  )) ?>
  <?php echo $this->htmlLink(array('action' => 'create', 'reset' => false), 
    $this->translate("Post New Announcement"),
    array(
      'class' => 'buttonlink',
      'style' => 'background-image: url(application/modules/Ynbusinesspages/externals/images/announcement/add.png);')) ?>
  <?php if(($this->paginator->getTotalItemCount()!=0) && (!$session->mobile)): ?>
    	<?php echo $this -> translate(array("(%s announcement total)", "(%s announcements total)" , $this->paginator->getTotalItemCount()), $this->paginator->getTotalItemCount());?>
  <?php endif;?>
  <?php echo $this->paginationControl($this->paginator); ?>
</div>

<br />
 <?php if(($this->paginator->getTotalItemCount()!=0) && ($session->mobile)): ?>
 	<div class="ymb_total_announcement">
 		<?php echo $this -> translate(array("(%s announcement total)", "(%s announcements total)" , $this->paginator->getTotalItemCount()), $this->paginator->getTotalItemCount());?>
    </div>
  <?php endif;?>
  
<?php if( count($this->paginator) ): ?>
  <table class='admin_table'>
    <thead>
      <tr>
        <th style="width: 1%;" class="admin_table_short"><input type='checkbox' class='checkbox'></th>
        <th style="width: 5%;"><a href="javascript:void(0);" onclick="javascript:changeOrder('announcement_id', '<?php if($this->orderby == 'announcement_id') echo "DESC"; else echo "ASC"; ?>');">
          <?php echo $this->translate("ID") ?>
        </a></th>
        <th><a href="javascript:void(0);" onclick="javascript:changeOrder('title', '<?php if($this->orderby == 'title') echo "DESC"; else echo "ASC"; ?>');">
          <?php echo $this->translate("Title") ?>
        </a></th>
        <th style="width: 30%;"><a href="javascript:void(0);" onclick="javascript:changeOrder('creation_date', '<?php if($this->orderby == 'creation_date') echo "DESC"; else echo "ASC"; ?>');">
          <?php echo $this->translate("Date") ?>
        </a></th>
        <th style="width: 20%;">
          <?php echo $this->translate("Options") ?>
        </th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($this->paginator as $item): ?>
      <tr>
        <td><input type='checkbox' class='checkbox' value="<?php echo $item->announcement_id?>"></td>
        <td><?php echo $item->announcement_id ?></td>
        <td class="admin_table_bold"><?php echo $item->title ?></td>
        <td><?php echo $this->locale()->toDateTime( $item->creation_date ) ?></td>
        <td class="admin_table_options">
          <?php echo $this->htmlLink(
            array('action' => 'edit', 'id' => $item->getIdentity(), 'reset' => false),
            $this->translate('edit')) ?> |
          <?php echo $this->htmlLink(
            array('action' => 'delete', 'id' => $item->getIdentity(), 'reset' => false),
            $this->translate('delete'),array('class' =>'smoothbox')) ?>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

<br/>
<div class='buttons'>
    <button  onclick="javascript:delectSelected();" type="submit">
      <?php echo $this->translate("Delete Selected") ?>
    </button>
</div>

<form id='delete_selected' method='post' action='<?php echo $this->url(array('action' =>'deleteselected')) ?>'>
  <input type="hidden" id="ids" name="ids" value=""/>
</form>

<?php else:?>

  <div class="tip">
    <span>
      <?php echo $this->translate("There are currently no announcements.") ?>
    </span>
  </div>

<?php endif; ?>

