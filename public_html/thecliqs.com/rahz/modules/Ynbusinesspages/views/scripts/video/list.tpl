<!-- Header -->
<div class="generic_layout_container layout_top">
	<div class="generic_layout_container layout_middle">
		<div class="headline">
		<h2>
			<?php echo $this->business->__toString()." ";
				echo $this->translate('&#187; Videos');
			?>
		</h2>
		</div>
	</div>
</div>

<div class="generic_layout_container layout_main ynbusinesspages_list">
	<div class="generic_layout_container layout_right">
		<!-- Search Form -->
		<div class="search">
			<?php echo $this->form->render($this);?>
		</div>
	</div>
	
	<div class="generic_layout_container layout_middle">
		<div class="generic_layout_container">
		<div class="ynbusinesspages-profile-module-header">
            <!-- Menu Bar -->
            <div class="ynbusinesspages-profile-header-right">
                <?php echo $this->htmlLink(array('route' => 'ynbusinesspages_profile', 'id' => $this->business->getIdentity(), 'slug' => $this->business-> getSlug(), 'tab' => $this -> tab), '<i class="fa fa-arrow-left"></i>'.$this->translate('Back to Business'), array(
					'class' => 'buttonlink'
				)) ?>
				<?php echo $this->htmlLink(array('route' => 'ynbusinesspages_extended', 'controller'=>'video','action'=>'manage','subject' => $this->subject()->getGuid()), '<i class="fa fa-user"></i>'.$this->translate('My Videos'), array(
					'class' => 'buttonlink'
				)) ?>
				<?php if( $this->canCreate ): ?>
					<?php echo $this->htmlLink(array(
							'route' => 'video_general',
							'action' => 'create',
							'parent_type' =>'ynbusinesspages_business',
							'subject_id' =>  $this->business->business_id,
						), '<i class="fa fa-plus-square"></i>'.$this->translate('Create New Video'), array(
						'class' => 'buttonlink'
						)) ;
					?>
				<?php endif; ?>
            </div>      
        </div>  		
		
		<!-- Content -->
		<?php if ($this->paginator->getTotalItemCount()> 0) : ?>
		<ul class="videos_browse" id="ynvideo_recent_videos">
			<?php foreach ($this->paginator as $item): ?>
			<li style="margin-right: 18px;">
				<?php
					echo $this->partial('_video_listing.tpl', 'ynbusinesspages', array(
						'video' => $item,
						'infoCol' => $this->infoCol,
					));
				?>
			</li>
			<?php endforeach; ?>
		</ul>
		<div class ="ynvideo_pages">
			<?php echo $this->paginationControl($this->paginator, null, null, array(
				'pageAsQuery' => true,
				'query' => $this->formValues,
			)); ?>
		</div>      
		<?php else : ?>
		<div class="tip">
			<span>
				<?php echo $this->translate('There is no video found.'); ?>
			</span>
		</div>
		<?php endif; ?>		
		</div>
	</div>
</div>
<!-- Menu Bar -->


<script type="text/javascript">
  en4.core.runonce.add(function()
  {
	  if($('title'))
	    {
	      new OverText($('title'), 
	      {
	        poll: true,
	        pollInterval: 500,
	        positionOptions: {
	          position: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
	          edge: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
	          offset: {
	            x: ( en4.orientation == 'rtl' ? -4 : 4 ),
	            y: 2
	          }
	        }
	      });
	    }
	 });
</script>