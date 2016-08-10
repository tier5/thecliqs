<?php
/**
 * SocialEngine
 *
 * @category   Application_Themes
 * @package    Template
 * @copyright  Copyright YouNet Company
 */
?>
<nav class="navbar navbar-default" role="navigation">
  <!-- Brand and toggle get grouped for better mobile display -->
  <div class="navbar-header">
    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex8-collapse">
      <span class="sr-only">Toggle navigation</span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
    </button>
	<div class="ybo_logo">
		<?php
		$title = Engine_Api::_()->getApi('settings', 'core')->getSetting('core_general_site_title', $this->translate('_SITE_TITLE'));
		
		$logo  = $this->logo ?$this->layout()->staticBaseUrl .'application/themes/'.YNRESPONSIVE_ACTIVE.'/images/logo.png': false;
    
		$route = $this->viewer()->getIdentity()
					 ? array('route'=>'user_general', 'action'=>'home')
					 : array('route'=>'default');
		
		echo ($logo)
			 ? $this->htmlLink($route, $this->htmlImage($logo, array('alt'=>$title, 'class' => 'navbar-brand')))
			 : $this->htmlLink($route, $title, array('class' => 'navbar-brand'));
		?>
	</div>
  </div>

  <!-- Collect the nav links, forms, and other content for toggling -->
  <div class="collapse navbar-collapse navbar-ex8-collapse">
    <ul class="nav navbar-nav navbar-right">
		<?php $count = 0;
		$limit = 5;
		foreach( $this->navigationMain as $item ): ?>
			<?php
			$check_active = $item->active;
			$request = Zend_Controller_Front::getInstance()->getRequest();
			$module = $request->getModuleName();
			$module_class = explode("_", $item->class);
            if(end($module_class) == $module && $module != 'user' && $module != 'core')
			{
				$check_active = true;
			}
			if($count < $limit):
				 $attribs = array_diff_key(array_filter($item->toArray()), array_flip(array(
			        'reset_params', 'route', 'module', 'controller', 'action', 'type',
			        'visible', 'label', 'href'
			        )));
				?>
		     <li<?php echo($check_active?' class="active"':'')?>>
          		<?php echo $this->htmlLink($item->getHref(), $this->translate($item->getLabel()), $attribs) ?>
        	</li>	
    <?php else:?>
    	 <?php if($count == $limit):?>
    	 		<li class="dropdown">
	        		<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $this -> translate("More");?> 
	        			<span class="glyphicon glyphicon-chevron-down btn-xs"></span>
	        		</a>
        			<ul class="dropdown-menu">
    	 	<?php endif;?>
    	 	<?php $attribs = array_diff_key(array_filter($item->toArray()), array_flip(array(
				        'reset_params', 'route', 'module', 'controller', 'action', 'type',
				        'visible', 'label', 'href'
				        )));
					?>
			     <li<?php echo($check_active?' class="active"':'')?>>
	          		<?php echo $this->htmlLink($item->getHref(), $this->translate($item->getLabel()), $attribs) ?>
	        	</li>	
    	 	<?php if($count > $limit && $count == count($this->navigationMain)):?>
    	 	</ul>
    	 </li>
    	 	<?php endif;?>
		<?php endif;
		$count ++;
		endforeach;?>
    </ul>
  </div><!-- /.navbar-collapse -->
</nav>