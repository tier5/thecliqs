<!-- Header -->
<div class="generic_layout_container layout_top">
	<div class="generic_layout_container layout_middle">
    <div class="headline">
		<h2>
			<?php echo $this->business->__toString()." ";
				echo $this->translate('&#187; Blog Entries');
			?>
		</h2>
    </div>
	</div>
</div>

<div class="generic_layout_container layout_main ynbusinesspages_list">
	<div class="generic_layout_container layout_right">
		<!-- Search Form -->
		<div class="blog_search_form">
			<?php echo $this->form->render($this);?>
		</div>
	</div>

	<div class="generic_layout_container layout_middle">
        <div class="generic_layout_container">
		<!-- Menu Bar -->
        <div class="ynbusinesspages-profile-module-header">
            <!-- Menu Bar -->
            <div class="ynbusinesspages-profile-header-right">
                <?php echo $this->htmlLink(array('route' => 'ynbusinesspages_profile', 'id' => $this->business->getIdentity(), 'slug' => $this->business-> getSlug(), 'tab' => $this -> tab), '<i class="fa fa-arrow-left"></i>'.$this->translate('Back to Business'), array(
                'class' => 'buttonlink'
                )) ?>

                <?php if ($this->canCreate):?>
                    <?php echo $this->htmlLink(array(
                    'route' => 'blog_general',
                    'controller' => 'index',
                    'action' => 'create',
                    'business_id' => $this->business->getIdentity(),
                    'parent_type' => 'ynbusinesspages_business',
                    ), '<i class="fa fa-plus-square"></i>'.$this->translate('Create Blog Entry'), array(
                    'class' => 'buttonlink'
                    ))
                    ?>
                <?php endif; ?>
            </div>      

            <div class="ynbusinesspages-profile-header-content">
               <?php if( $this->paginator->getTotalItemCount() > 0 ): $business = $this->business;?>
                <span class="ynbusinesspages-numeric"><?php echo $this->paginator->getTotalItemCount(); ?></span>
                <?php echo $this-> translate(array("ynbusiness_blog", "Blog entries", $this->paginator->getTotalItemCount()), $this->paginator->getTotalItemCount());?>
                <?php endif; ?>
            </div>
        </div>        
		
		<!-- Content -->
		<?php if( $this->paginator->getTotalItemCount() > 0 ): $business = $this->business;?>	    
		<ul class="ynbusinesspages_blog">  	
		<?php foreach ($this->paginator as $blog): 
			$owner = Engine_Api::_() -> getDbTable('mappings', 'ynbusinesspages') -> getOwner($blog);?>
            <li>
                <div class="wrap_col3">
                    <div class="wrap_col_left">
                        <div class="ynblog_entrylist_entry_date">
                            <?php 
                            $creation_date = new Zend_Date(strtotime($blog->creation_date)); 
                            $creation_date->setTimezone($this->timezone);
                            ?>
                            <div class="day">
                                <?php echo $creation_date->get(Zend_Date::DAY)?>
                            </div>
                            <div class="month">
                            <?php echo $creation_date->get(Zend_Date::MONTH_NAME_SHORT)?>
                            </div>
                            <div class="year">
                            <?php echo $creation_date->get(Zend_Date::YEAR)?>
                            </div>
                        </div>
                    </div>
                    <div class="wrap_col_center">
                        <div class="yn_title"><?php echo $this->htmlLink($blog->getHref(), $blog->getTitle()) ?></div>
                        <div class="post_by"><?php echo $this->translate('by');?> <?php echo $this->htmlLink($owner->getHref(), $owner->getTitle()) ?></div>
                        <div class="ynblog_entrylist_entry_body"><?php echo $this->string()->truncate($this->string()->stripTags($blog->body), 300) ?></div>
                    </div>                    
                </div>

                <?php 
                $canRemove = $business->isAllowed('blog_delete', null, $blog);
                $canEdit = $blog->authorization()->isAllowed(null, 'edit');
                $canDelete = $blog->authorization()->isAllowed(null, 'delete');
                ?>
                <?php if ($canRemove || $canDelete || $canEdit): ?>
                <div class="ynbusinesspages-profile-module-option">
                    <?php if ($canEdit): ?>
                        <?php echo $this->htmlLink(
                        array(
                            'action' => 'edit',
                            'blog_id' => $blog->getIdentity(),
                            'route' => 'blog_specific',
                            'reset' => true,
                            'business_id' => $this->subject()->getIdentity(),
                            'parent_type' => 'ynbusinesspages_business',
                        ), '<i class="fa fa-pencil-square-o"></i>'.$this->translate('Edit Entry'), array(
                            'class' => 'buttonlink',
                        ))
                        ?>
                    <?php endif; ?>
                    <?php if ($canDelete): ?>
                        <?php echo $this->htmlLink(array(
                            'route' => 'blog_specific',
                            'action' => 'delete',
                            'blog_id' => $blog->getIdentity(),
                            'business_id' => $this->subject()->getIdentity(),
                            'parent_type' => 'ynbusinesspages_business',
                            'format' => 'smoothbox'
                        ),
                        '<i class="fa fa-trash-o"></i>'.$this->translate('Delete Entry'),
                        array('class'=>'buttonlink smoothbox'))
                        ?>
                    <?php endif; ?> 
                    <?php if ($canRemove): ?>
                        <?php echo $this->htmlLink(array(
                            'route' => 'ynbusinesspages_specific',
                            'action' => 'remove-item',
                            'item_id' => $blog->getIdentity(),
                            'item_type' => 'blog',
                            'item_label' => 'Blog',
                            'remove_action' => 'blog_delete',
                            'business_id' => $business->getIdentity(),
                        ),
                        '<i class="fa fa-times"></i>'.$this->translate('Delete Entry To Business'),
                        array('class'=>'buttonlink smoothbox'))
                        ?>
                    <?php endif; ?> 
                </div>
                <?php endif; ?>
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
			  <?php echo $this->translate('No blog entries have been created.');?>
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
  