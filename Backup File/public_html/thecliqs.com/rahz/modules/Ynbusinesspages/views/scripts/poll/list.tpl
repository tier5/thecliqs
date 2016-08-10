<!-- Header -->
<div class="generic_layout_container layout_top">
	<div class="generic_layout_container layout_middle">
    <div class="headline">
		<h2>
			<?php echo $this->business->__toString();
				echo $this->translate('&#187; Polls');
			?>
		</h2>
        </div>
	</div>
</div>
<div class="generic_layout_container layout_main ynbusinesspages_list">
	<div class="generic_layout_container layout_right">
		<!-- Search Form -->
		<div class="poll_search_form">
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
                <?php if ($this->canCreate):?>
                    <?php echo $this->htmlLink(array(
                        'route' => 'poll_general',
                        'controller' => 'index',
                        'action' => 'create',
                        'business_id' => $this->business->getIdentity(),
                        'parent_type' => 'ynbusinesspages_business',
                    ), '<i class="fa fa-plus-square"></i>'.$this->translate('Create New Poll'), array(
                        'class' => 'buttonlink'
                    ))
                    ?>
                <?php endif; ?>
            </div>      
            <?php if( $this->paginator->getTotalItemCount() > 0 ): $business = $this->business;?>
            <div class="ynbusinesspages-profile-header-content">
                <span class="ynbusinesspages-numeric"><?php echo $this->paginator->getTotalItemCount(); ?></span>
                <?php echo $this-> translate(array("ynbusiness_poll", "Polls", $this->paginator->getTotalItemCount()), $this->paginator->getTotalItemCount());?>
            </div>
            <?php endif; ?>
        </div>  
		
		<!-- Content -->
		<?php if( $this->paginator->getTotalItemCount() > 0 ): $business = $this->business;?>
		<ul class="ynbusinesspages_poll polls_browse">  
            <?php foreach ($this->paginator as $poll): 
            	$owner = Engine_Api::_() -> getDbTable('mappings', 'ynbusinesspages') -> getOwner($poll);?>
            <li id="poll-item-<?php echo $poll->poll_id ?>">
                <?php echo $this->htmlLink(
                    $poll->getHref(),
                    $this->itemPhoto($owner, 'thumb.icon', $owner->getTitle()),
                    array('class' => 'polls_browse_photo')
                ) ?>
                <div class="polls_browse_info">
                    <h3 class="polls_browse_info_title">
                        <?php echo $this->htmlLink($poll->getHref(), $poll->getTitle()) ?>
                        <?php if( $poll->closed ): ?>
                        <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Poll/externals/images/close.png' alt="<?php echo $this->translate('Closed') ?>" />
                        <?php endif ?>
                    </h3>
                    <div class="polls_browse_info_date">
                        <?php echo $this->translate('Posted by %s', $this->htmlLink($owner, $owner->getTitle())) ?>
                        <?php echo $this->timestamp($poll->creation_date) ?>
                    </div>
                    <div class="polls_browse_info_vote">
                        <?php echo $this->translate(array('%s vote', '%s votes', $poll->vote_count), $this->locale()->toNumber($poll->vote_count)) ?>
                        -
                        <?php echo $this->translate(array('%s view', '%s views', $poll->view_count), $this->locale()->toNumber($poll->view_count)) ?>
                    </div>
                    <?php if (!empty($poll->description)): ?>
                    <div class="polls_browse_info_desc">
                    <?php echo $poll->description ?>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="ynbusinesspages-profile-module-option">
                    <?php 
                    $canRemove = $business -> isAllowed('poll_delete', null, $poll);
                    $canDelete = $poll->authorization()->isAllowed(null, 'delete');
                    $canEdit = $poll->authorization()->isAllowed(null, 'edit');
                    ?>
                    <?php if ($canRemove || $canDelete || $canEdit): ?>
                    <?php if ($canEdit): ?>
                        <?php echo $this->htmlLink(
                        array(
                            'action' => 'edit',
                            'poll_id' => $poll->getIdentity(),
                            'route' => 'poll_specific',
                            'reset' => true,
                            'business_id' => $this->subject()->getIdentity(),
                            'parent_type' => 'ynbusinesspages_business',
                        ), '<i class="fa fa-pencil-square-o"></i>'.$this->translate('Edit Poll'), array(
                            'class' => 'buttonlink',
                        ))
                        ?>
                    <?php endif; ?>
                    <?php if ($canDelete): ?>
                        <?php echo $this->htmlLink(array(
                            'route' => 'poll_specific',
                            'action' => 'delete',
                            'poll_id' => $poll->getIdentity(),
                            'format' => 'smoothbox',
                            'business_id' => $business->getIdentity(),
                            'parent_type' => 'ynbusinesspages_business',
                        ),
                        '<i class="fa fa-trash-o"></i>'.$this->translate('Delete Poll'),
                        array('class'=>'buttonlink smoothbox'))
                      ?>
                    <?php endif; ?>
                    <?php if ($canRemove): ?>
                        <?php echo $this->htmlLink(array(
                            'route' => 'ynbusinesspages_specific',
                            'action' => 'remove-item',
                            'item_id' => $poll->getIdentity(),
                            'item_type' => 'poll',
                            'item_label' => 'Poll',
                            'remove_action' => 'poll_delete',
                            'business_id' => $business->getIdentity(),
                        ),
                        '<i class="fa fa-times"></i>'.$this->translate('Delete Poll To Business'),
                        array('class'=>'buttonlink smoothbox'))
                        ?>
                    <?php endif; ?>  
                    <?php endif; ?>
                </div>
            </li>
            <?php endforeach; ?>             
        </ul>  
		<?php if( $this->paginator->count() > 0 ): ?>
			<?php echo $this->paginationControl($this->paginator, null, null, array(
				'pageAsQuery' => true,
				'query' => $this->formValues,
			)); ?>
		<?php endif; ?>
		<?php else: ?>
		<div class="tip">
			<span>
			  <?php echo $this->translate('No polls have been created.');?>
			</span>
		</div>
		<?php endif; ?>
        </div>
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
  