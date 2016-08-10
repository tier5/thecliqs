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


<div class="admin_home_environment">
  <h3 class="sep">
    <span><?php echo $this->translate('Permission Mode'); ?></span>
  </h3>
  <div class="admin_home_environment_buttons">
    <button class="button_disabled" onclick="changePermissionMode('level', this);this.blur();">
			<?php echo $this->translate('Member Level Mode'); ?>
		</button>
    <button onclick="changePermissionMode('package', this);this.blur();">
			<?php echo $this->translate('Package Mode'); ?>
		</button>
  </div>

  <br>

  <div class="admin_home_environment_description">
    <?php echo $this->translate('PAGE_ADMINPERMISSION_MODE_DESCRIPTION'); ?>
	</div>

  <script type="text/javascript">
  //&lt;![CDATA[
  var changePermissionMode = function(mode, btn) {
    $$('div.admin_home_environment button').set('class', 'button_disabled');
    btn.set('class', '');
    $$('div.admin_home_environment_description').set('text', 'Changing mode - please wait...');
    new Request.JSON({
      url: '<?php echo $this->url(array('module'=>'page', 'controller'=>'permission'), 'admin_default'); ?>',
      method: 'post',
			data: {'format':'json', 'permission_mode':mode},
      onSuccess: function(responseJSON){
        if ($type(responseJSON) == 'object') {
          if (responseJSON.success || !$type(responseJSON.error))
            window.location.href = window.location.href;
          else
            alert(responseJSON.error);
        } else
          alert('An unknown error occurred; changes have not been saved.');
      }
    }).send();
  }
  //]]&gt;
  </script>
</div>
<br/>


<p>
  <?php echo $this->translate("This is a list all packages. Use form to filter packages.") ?>
</p>
<br />

<div class='admin_search'>
	<?php echo $this->filterForm->render($this); ?>
</div>
<br />

<div class='admin_results'>
  <div>
    <?php $count = $this->paginator->getTotalItemCount() ?>
    <?php echo $this->translate(array("%s package found", "%s packages found", $count),
        $this->locale()->toNumber($count)) ?>
  </div>
  <div>
    <?php echo $this->paginationControl($this->paginator, null, null, array(
      'pageAsQuery' => true,
      'query' => $this->formValues,
      'params' => $this->formValues
    )); ?>
  </div>

	<?php echo $this->htmlLink(
		$this->url(array('module'=>'page', 'controller'=>'packages', 'action'=>'create'), 'admin_default', true),
		$this->translate('PAGE_PACKAGE_Add New Package'),
		array('class'=>'buttonlink product_icon_create')
	); ?>
</div>
<br />
	
<?php if( count($this->paginator) ): ?>
  <form id='multidelete_form' method="post" action="<?php echo $this->url(array('module' => 'page', 'controller' => 'packages', 'action' => 'save-order'), 'admin_default');?>" onSubmit="return multiDelete();">
    <table class='admin_table page_packages'>
      <thead>
        <tr>
          <th class='admin_table_short'><a href="javascript:void(0);" onclick="javascript:changeOrder('package_id', 'DESC');">ID</a></th>
          <th><a href="javascript:void(0);" onclick="javascript:changeOrder('name', 'ASC');"><?php echo $this->translate("Title") ?></a></th>
          <th><a href="javascript:void(0);" onclick="javascript:changeOrder('price', 'ASC');"><?php echo $this->translate("Price") ?></a></th>
          <th><a href="javascript:void(0);" onclick="javascript:changeOrder('duration', 'ASC');"><?php echo $this->translate("Duration") ?></a></th>
          <th class="center"><a href="javascript:void(0);" onclick="javascript:changeOrder('enabled', 'ASC');"><?php echo $this->translate("Default") ?></a></th>
          <th class="center"><a href="javascript:void(0);" onclick="javascript:changeOrder('totalpages', 'ASC');"><?php echo $this->translate("Total Pages") ?></a></th>
          <th class="center"><?php echo $this->translate("Options") ?></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($this->paginator as $item): ?>
          <tr class="<?php if ($item->sponsored) echo "admin_featured_page"; ?>">
            <td><?php echo $item->getIdentity() ?></td>
            <td><?php echo $this->htmlLink($item->getHref(), ($item->name ? $item->name : "<i>".$this->translate("Untitled")."</i>" )); ?></td>
            <td>
              <?php echo $this->locale()->toCurrency($item->price,Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD')) ?>
            </td>
            <td><?php echo $item->getPackageDescription(); ?></td>
            <td class="center">
              <?php if (!$item->isDefault() && $item->isFree() && $item->isOneTime() && $item->isForever()){ ?>
              <a href="<?php echo $this->url(array('action' => 'make-default', 'package_id' => $item->getIdentity())); ?>" class="smoothbox" >
                <input type='radio' name="default" title="<?php echo $this->translate('Make Default'); ?>" style="cursor: pointer"/>
              </a>
              <?php }elseif($item->getIdentity() == $this->default){ ?>
                <?php echo $this->htmlImage('application/modules/Core/externals/images/notice.png', $this->translate('default'), array('title'=>$this->translate('Default'))); ?>
              <?php } ?>
            </td>
            <td class="center"><?php echo $item->totalpages; ?></td>
            <td class="center">
            <?php echo $this->htmlLink(
              $this->url(array('module'=>'page', 'controller'=>'packages', 'action'=>'edit', 'package_id'=>$item->getIdentity()), 'admin_default', true),
              $this->translate('Edit')
            ); ?>
              |
            <?php echo $this->htmlLink(array('route'=>'page_admin_manage', 'package'=>$item->getIdentity()), $this->translate('View Pages'), array('target'=>'_blank')); ?>
            <?php if( !$item->isDefault() ): ?>
              |
            <?php echo $this->htmlLink(
              array('route'=>'page_admin_packages', 'action'=>'delete', 'package_id'=>$item->getIdentity()),
              $this->translate('Delete'), array('class'=>'smoothbox')); ?>
            </td>
            <?php endif; ?>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
      <br />

    <div class='buttons'>
      <!--<button type='submit'><?php /*echo $this->translate("Save Selected") */?></button>-->
    </div>  
  </form>
  
<?php else:?>
  <div class="tip">
    <span>
      <?php echo $this->translate("pages_admin there are no pages") ?>
    </span>
  </div>
<?php endif; ?>