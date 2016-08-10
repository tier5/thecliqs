<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2010-08-31 17:53 idris $
 * @author     Idris
 */
?>

<script type="text/javascript">
function multiAction()
{
  return confirm(en4.core.language.translate("Are you sure you want to " + $('action_type').value + " the selected pages?"));
}


function selectAll()
{
  var i;
  var multidelete_form = $('multidelete_form');
  var inputs = multidelete_form.elements;
  for (i = 1; i < inputs.length; i++) {
    if (!inputs[i].disabled) {
      inputs[i].checked = inputs[0].checked;
    }
  }
}

function confirmDelete(page_id)
{
  if (confirm('<?php echo $this->string()->escapeJavascript($this->translate("Are you sure you want to delete this page?")) ?>')){
	  window.location.href = '<?php echo $this->url(array('action' => 'delete'), 'page_admin_manage', true); ?>/'+page_id;
  }else{
	  return false;
  }
}

var currentOrder = '<?php echo $this->order ?>';
var currentOrderDirection = '<?php echo $this->order_direction ?>';

var changeOrder = function(order, default_direction){
  if( order == currentOrder ) {
    $('order_direction').value = ( currentOrderDirection == 'ASC' ? 'DESC' : 'ASC' );
  } else {
    $('order').value = order;
    $('order_direction').value = default_direction;
  }
  $('filter_form').submit();
}

</script>

<h2><?php echo $this->translate("Page Plugin") ?></h2>

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
  <?php echo $this->translate("This is a list all pages created by your users. You can approve, disapprove or delete them. Use form to filter pages.") ?>
</p>
<br />

<div class='admin_search' style="max-width: 945px">
	<?php echo $this->filterForm->render($this); ?>
</div>
<br />

<div class='admin_results'>
  <div>
    <?php $pageCount = $this->paginator->getTotalItemCount() ?>
    <?php echo $this->translate(array("%s page found", "%s pages found", $pageCount), ($pageCount)) ?>
  </div>
  <div>
    <?php echo $this->paginationControl($this->paginator, null, null, array(
      'pageAsQuery' => true,
      'query' => $this->formValues,
      //'params' => $this->formValues,
    )); ?>
  </div>
</div>
<br />
	
<?php if( count($this->paginator) ): ?>
  <form id='multidelete_form' method="post" action="<?php echo $this->url(array('module' => 'page', 'controller' => 'manage', 'action' => 'delete-all'), 'admin_default');?>" onSubmit="return multiAction();">
    <table class='admin_table'>
      <thead>
        <tr>
          <th class='admin_table_short'><input onclick='selectAll();' type='checkbox' class='checkbox' /></th>
          <th class='admin_table_short'><a href="javascript:void(0);" onclick="javascript:changeOrder('page_id', 'DESC');">ID</a></th>
          <th><a href="javascript:void(0);" onclick="javascript:changeOrder('title', 'ASC');"><?php echo $this->translate("Title") ?></a></th>
          <th><a href="javascript:void(0);" onclick="javascript:changeOrder('category', 'ASC');"><?php echo $this->translate("Category") ?></a></th>
          <?php if($this->isPackageEnabled) : ?>
            <th><a href="javascript:void(0);" onclick="javascript:changeOrder('package', 'ASC')"> <?php echo $this->translate('PAGE_Package')?> </a></th>
          <?php endif; ?>
          <th><?php echo $this->translate("Owner") ?></th>
          <th class="center"><a href="javascript:void(0);" onclick="javascript:changeOrder('view_count', 'ASC');"><?php echo $this->translate("Views") ?></a></th>
          <th class="center"><a href="javascript:void(0);" onclick="javascript:changeOrder('modified_date', 'ASC');"><?php echo $this->translate("Date") ?></a></th>
          <th class="center"><?php echo $this->translate("Options") ?></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($this->paginator as $item):	 ?>
        <?php
          if($item->name == 'default') continue;
        ?>
          <tr class="<?php if ($item->sponsored) echo "admin_featured_page"; ?>">
            <td><input type='checkbox' name='items[]' class='checkbox' value="<?php echo $item->page_id ?>"/></td>
            <td><?php echo $item->getIdentity() ?></td>
            <td><?php echo $this->htmlLink($item->getHref(), ($item->getTitle() ? $item->getTitle() : "<i>".$this->translate("Untitled")."</i>" )); ?></td>
            <td><?php echo ($item->category ? $item->category : ("<i>".$this->translate("Uncategorized")."</i>")); ?></td>
            <?php if($this->isPackageEnabled): ?>
              <td><?php echo ($item->package) ? $item->package : '-';

                echo '<span> </span>('.$this->htmlLink(
                  array(
                    'route' => 'page_admin_manage',
                    'action' => 'manage-package',
                    'page_id' => $item->page_id,
                    'format' => 'smoothbox',
                  ),
                  'more',
                  array('class' => 'smoothbox')
                ).')'; ?>
              </td>
            <?php endif; ?>
            <td><?php echo $this->htmlLink($this->user($item->user_id)->getHref(), $this->user($item->user_id)->getTitle()); ?></td>
            <td class="center"><?php echo $this->locale()->toNumber($item->view_count) ?></td>
            <td class="center"><?php echo $item->creation_date ?></td>
            <td class="center">
            <?php 

              //enabled
              echo $this->htmlLink(
                array(
                  'route' => 'page_admin_manage',
                  'action' => 'enable',
                  'page_id' => $item->page_id,
                  'value' => 1-$item->enabled
                ),
                '<img title="'.$this->translate('PAGE_enabled'.$item->enabled).'" class="page-icon" src="application/modules/Page/externals/images/approved'.$item->enabled.'.png">'
              );

	            //sponsored
							echo $this->htmlLink(
                 array(
                     'action' => 'sponsor',
                     'page_id' => $item->page_id,
                     'value' => 1-$item->sponsored
                 ),
                 '<img title="'.$this->translate('PAGE_sponsored'.$item->sponsored).'" class="page-icon" src="application/modules/Page/externals/images/sponsored'.$item->sponsored.'.png">'
              );
	            //featured
              echo $this->htmlLink(
               array(
                   'action' => 'feature',
                   'page_id' => $item->page_id,
                   'value' => 1-$item->featured
               ),
                 '<img title="'.$this->translate('PAGE_featured'.$item->featured).'" class="page-icon" src="application/modules/Page/externals/images/featured'.$item->featured.'.png">'
              );

              //approved
              echo $this->htmlLink(
                array(
                  'route' => 'page_admin_manage',
                  'action' => 'approve',
                  'page_id' => $item->page_id,
                  'value' => 1-$item->approved
                ),
                '<img title="'.$this->translate('PAGE_approved'.$item->approved).'" class="page-icon" src="application/modules/Page/externals/images/approved'.$item->approved.'.png">'
              );
              ?>
	            <?php echo $this->htmlLink(
                'javascript:void(0)',
                '<img class="page-icon" title="'.$this->translate('Delete').'" src="application/modules/Core/externals/images/delete.png">',
                array('onClick' => "confirmDelete({$item->getIdentity()})"
              )) ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <br />

    <div class='buttons'>
      <select name="action_type" id="action_type">
        <option label="Delete" value="delete">Delete</option>
        <option label="Approve" value="approve">Set Approved</option>
        <option label="Feature" value="feature">Set Featured</option>
        <option label="Sponsore" value="sponsore">Set Sponsored</option>
        <option label="Enable" value="enable">Set Enabled</option>
        <option label="Disable" value="disable">Set Disabled</option>
        <option label="Disapprove" value="disapprove">Set Disapproved</option>
        <option label="Not Sponsored" value="notsponsore">Set Not Sponsored</option>
        <option label="Not Featured" value="notfeature">Set Not Featured</option>
      </select>
      <span> Selected </span>
      <button type='submit'><?php echo $this->translate("Submit") ?></button>
    </div>
  </form>
  
<?php else:?>
  <div class="tip">
    <span>
      <?php echo $this->translate("pages_admin there are no pages") ?>
    </span>
  </div>
<?php endif; ?>