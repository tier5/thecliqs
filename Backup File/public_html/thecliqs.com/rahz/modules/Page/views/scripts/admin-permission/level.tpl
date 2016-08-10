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

<h2><?php echo $this->translate("Page Plugin") ?></h2>

<?php if( count($this->navigation) ): ?>
  <div class='page_admin_tabs'>
    <?php
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<div class="admin_home_environment">
  <h3 class="sep">
    <span><?php echo $this->translate('Permission Mode'); ?></span>
  </h3>
  <div class="admin_home_environment_buttons">
    <button onclick="changePermissionMode('level', this);this.blur();">
			<?php echo $this->translate('Member Level Mode'); ?>
		</button>
    <button class="button_disabled" onclick="changePermissionMode('package', this);this.blur();">
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

<div class='clear'>
  <div class='settings'>
    <?php echo $this->form->render($this); ?>
  </div>
</div>

<script type="text/javascript">
//<![CDATA[
window.addEvent('domready', function(){
	$('level_id').addEvent('change', function(){
		  window.location.href = en4.core.baseUrl + 'admin/page/permission/level/'+this.get('value');
	});
});
//]]>
</script>