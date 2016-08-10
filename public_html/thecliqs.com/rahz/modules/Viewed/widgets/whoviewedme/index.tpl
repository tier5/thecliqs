<?php
/**
 * Integration4us
 *
 * @category   Application_Widget
 * @package    Who Viewed Me Widget
 * @copyright  Copyright 2009-2010 Integration4us
 * @license    http://www.integration4us.com/terms
 * @author     Jomar
 */
 ?>
 <?php $viewer = Engine_Api::_()->user()->getViewer();?>
 <div class='layout_user_list_signups'>
 	<h3><?php echo $this->translate("Who Viewed Me");?></h3>
	  <?php 
		$arr_all = $this->paginator;
		if(!empty($arr_all))
		 {  
		?>
		 <ul>
		 <?php  foreach( $arr_all as $key =>$arr ){ ?>
		  	<?php $viewed = Engine_Api::_()->getItem('user', $arr->user_id) ?>		
				     <li style = margin-left:0px;">
				     <?php echo $this->htmlLink($viewed->getHref(), $this->itemPhoto($viewed, 'thumb.icon'),array('class'=>'newestmembers_thumb','style'=>'margin-left:0px;')) ?>				
					  	<div class="newestmembers_info">
					      <div class="newestmembers_name">
					      	<?php echo $this->htmlLink($viewed->getHref(), $viewed->getTitle()) ?>
					       </div>
					      <div class='newestmembers_date'>
					      	<?php echo $this->timestamp($arr->datetime) ?>
					      </div>
					      <div class='newestmembers_date'>
					     <span><?php echo $this->translate("View Count:"); ?></span><span><?php echo $arr->count; ?></span>
					    </div>
					    </div>
					    <div>
					     <?php 
					        $table = Engine_Api::_()->getDbtable('block', 'user');
					        $select = $table->select()
					          ->where('user_id = ?', $viewed->getIdentity())
					          ->where('blocked_user_id = ?', $viewer->getIdentity())
					          ->limit(1);
					        $row = $table->fetchRow($select);
					        ?>
					        <?php if( $row == NULL ): ?>
					          <?php if( $this->viewer()->getIdentity() ): ?>
					          <div class='browsemembers_results_links'>
					            <?php echo $this->userFriendship($viewed) ?>
					          </div>
					        <?php endif; ?>
					        <?php endif; ?>
					    </div>
				    </li>

		<?php  }?> <!-- ending loop -->
		<li class="active">
		<?php if(count($this->paginator)<=0):?>
			<p><?php echo $this->translate("No one has viewed your profile yet."); ?></p>
		<?php endif; ?>
		<?php if(count($this->paginator)>0):?>
		<?php if($this->userallow):?>
		  <?php echo $this->htmlLink(array('route' => 'default','module' => 'viewed', 'controller' => 'index'),$this->translate('View All'),array('class' =>'button'));?>	
		<?php else :?>
		<?php echo $this->htmlLink(array('route' => 'default','module' => 'viewed', 'controller' => 'subscription','action'=>'choose'),$this->translate('View All'),array('class'=>'button'));?>
		<?php endif;?>
		<?php endif;?>
		</li>
		</ul>
	<?php 
	}
	else
	{
	?>
	<ul>
	<li class="active">
	<p><?php echo $this->translate("Please buy subscription."); ?></p>
	 <?php echo $this->htmlLink(array('route' => 'default','module' => 'viewed', 'controller' => 'subscription','action'=>'choose'),$this->translate('Buy'),array('class'=>'button'));?>	
	 </li>
	 </ul>
	 <?php
	 }
	 ?>
</div>