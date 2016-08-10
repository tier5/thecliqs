<!-- Header -->
<div class="generic_layout_container layout_top">
	<div class="generic_layout_container layout_middle">
		<h2>
			<?php echo $this->listing->__toString();
				echo $this->translate(' &#187; Discussions');
			?>
		</h2>
	</div>
</div>
<div class="generic_layout_container layout_main ynlistings_list">
	<div class="generic_layout_container layout_right">
		<!-- Search Form -->
		<div class="topic_search_form">
			<?php echo $this->form->render($this);?>
		</div>
	</div>
	
	<div class="generic_layout_container layout_middle">
		<!-- Menu Bar -->
		<div class="ynlistings_discussions_options">
			<?php echo $this->htmlLink(array('route' => 'ynlistings_general','action' => 'view', 'id' => $this->listing->getIdentity()), $this->translate('Back to Listing'), array(
				'class' => 'buttonlink icon_back'
			)) ?>
			<?php if ($this->can_post) {
				echo $this->htmlLink(array('route' => 'ynlistings_extended', 'controller' => 'topic', 'action' => 'create', 'subject' => $this->listing->getGuid()), $this->translate('Post New Topic'), array(
				'class' => 'buttonlink icon_ynlistings_post_new'
				)) ;
			}?>
		</div>
		
		<!-- Content -->
		<?php if( count($this->paginator) > 0 ): ?>
		<ul class="ynlistings_discussions">
			<?php foreach( $this->paginator as $topic ):
				$owner = $topic->getOwner();
				$lastpost = $topic->getLastPost();
				$lastposter = $topic->getLastPoster();
			?>
			<li>
				<div class="ynlistings_discussions_lastreply">
					<?php echo $this->htmlLink($owner->getHref(), $this->itemPhoto($owner, 'thumb.icon')) ?>
					<div class="ynlistings_discussions_lastreply_info">
						<b><?php echo $owner->__toString() ?></b>
					</div>
				</div>
				<div class="ynlistings_discussions_replies">
					<span>
						<?php echo $this->locale()->toNumber($topic->post_count - 1) ?>
					</span>
					<?php echo $this->translate(array('reply', 'replies', $topic->post_count - 1)) ?>
				</div>
				<div class="ynlistings_discussions_info">
					<h3<?php if( $topic->sticky ): ?> class='ynlistings_discussions_sticky'<?php endif; ?>>
					<?php echo $this->htmlLink($topic->getHref(), $topic->getTitle()) ?>
					</h3>
					<div class="ynlistings_discussions_blurb" style="text-align: justify;">
						<?php echo $this->viewMore(strip_tags($topic->getDescription())) ?>
					</div>
					<?php echo $this->htmlLink($lastpost->getHref(), $this->translate('Last Replied')) ?>
					<?php echo $this->translate('by');?> <?php echo $lastposter->__toString() ?>
					-
					<?php echo $this->timestamp(strtotime($topic->modified_date)) ?>
				</div>
			</li>
		<?php endforeach; ?>
		</ul>
		<div>
			<?php echo $this->paginationControl($this->paginator, null, null, array(
				'pageAsQuery' => true,
				'query' => $this->formValues,
			)); ?>
		</div>
		<?php else:?>
		<div class="tip">
			<span>
				<?php echo $this->translate('No topics have been posted in this listing yet.');?>
				<?php if($this->canCreate):?>
					<?php echo $this->translate('Create a %1$snew one%2$s',
					'<a href="'.$this->url(array('controller'=>'topic','action' => 'create','subject' =>$this->listing->getGuid()), 'ynlistings_extended').'">', '</a>');?>
				<?php endif;?>
			</span>
		</div>
		<?php endif; ?>
	</div>
</div>

<script type="text/javascript">
  en4.core.runonce.add(function()
  {
	  if($('search'))
	    {
	      new OverText($('search'), 
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