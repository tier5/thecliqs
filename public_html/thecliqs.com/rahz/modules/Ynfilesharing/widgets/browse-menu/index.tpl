<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynfilesharing
 * @author     YouNet Company
 */
?>
<div class="headline">
  <h2>
    <?php echo $this->translate('File Sharing') ?>
  </h2>
 <div class="tabs">
	<ul class="navigation">
	<?php
		foreach( $this->navigation as $item ): ?>
			<?php
			$check_active = $item->active;
			$request = Zend_Controller_Front::getInstance()->getRequest();
			$module = $request->getModuleName();
			$action = $request->getActionName();
			$module_class = explode(" ", $item->class);
			if(end($module_class) == $module."_main_".$action)
			{
				$check_active = true;
			}
			 $attribs = array_diff_key(array_filter($item->toArray()), array_flip(array(
		        'reset_params', 'route', 'module', 'controller', 'action', 'type',
		        'visible', 'label', 'href'
		        )));
			?>
	     <li<?php echo($check_active?' class="active"':'')?>>
        		<?php echo $this->htmlLink($item->getHref(), $this->translate($item->getLabel()), $attribs) ?>
      	</li>	
   <?php endforeach;?>
	</ul>        	
</div>
</div>