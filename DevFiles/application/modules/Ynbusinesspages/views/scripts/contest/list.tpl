<!-- Header -->
<div class="generic_layout_container layout_top">
	<div class="generic_layout_container layout_middle">
        <div class="headline">
		<h2>
			<?php echo $this->business->__toString()." ";
				echo $this->translate('&#187; Contests');
			?>
		</h2>
        </div>
	</div>
</div>
<div class="generic_layout_container layout_main ynbusinesspages_list">
	<div class="generic_layout_container layout_right">
		<!-- Search Form -->
		<div class="contest_search_form">
			<?php echo $this->form->render($this);?>
		</div>
	</div>
	<div class="generic_layout_container layout_middle">
        <div class="generic_layout_container">
		<!-- Menu Bar -->
        <div class="ynbusinesspages-profile-module-header">
            <div class="ynbusinesspages-profile-header-right">
                <?php echo $this->htmlLink(array('route' => 'ynbusinesspages_profile', 'id' => $this->business->getIdentity(), 'slug' => $this->business-> getSlug(), 'tab' => $this -> tab), '<i class="fa fa-arrow-left"></i>'.$this->translate('Back to Business'), array(
                'class' => 'buttonlink'
                )) ?>

                <?php if ($this->canCreate):?>
                    <?php echo $this->htmlLink(array(
                    'route' => 'yncontest_mycontest',
                    'controller' => 'my-contest',
                    'action' => 'create-contest',
                    'business_id' => $this->business->getIdentity(),
                    'parent_type' => 'ynbusinesspages_business',
                    ), '<i class="fa fa-plus-square"></i>'.$this->translate('Post New Contest'), array(
                    'class' => 'buttonlink'
                    ))
                    ?>
                <?php endif; ?>
            </div>

            <div class="ynbusinesspages-profile-header-content">
                <?php if( $this->paginator->getTotalItemCount() > 0 ): $business = $this->business;?>
                <span class="ynbusinesspages-numeric"><?php echo $this->paginator->getTotalItemCount(); ?></span>
                <?php echo $this-> translate(array("ynbusiness_contest", "Contests", $this->paginator->getTotalItemCount()), $this->paginator->getTotalItemCount());?>
                <?php endif; ?>
            </div>
        </div>
		
		<!-- Content -->
		<?php if( $this->paginator->getTotalItemCount() > 0 ): $business = $this->business;?>
		<ul class="ynbusinesspages_contest contest_browse large_contest_list">  
            <?php $zebra = 0;?>         
            <?php foreach ($this->paginator as $contest): 
            	$owner = Engine_Api::_() -> getDbTable('mappings', 'ynbusinesspages') -> getOwner($contest);?>
            <li class="clearfix <?php echo ($zebra % 2) == 0? 'odd' : 'even';?>">
                <div class="contest_large_img" style="background-image:url(<?php echo $contest->getPhotoUrl('thumb.profile');?>);">
                    <div class="corner_icon <?php echo Engine_Api::_()->yncontest()->arrPlugins[$contest->contest_type]?>"></div>
                    <div class="wrap_link">
                    <?php if($contest->contest_status == 'close'):?>
                        <div class="link"><span class="close"><?php echo $this->translate('CLOSED');?></span></div>                 
                    <?php else:?>
                        <?php if($contest->featured_id):?>
                            <div class="link"><span class="feature"><?php echo $this->translate('FEATURE');?></span></div>      
                        <?php endif; ?>
                        <?php if($contest->premium_id):?>
                        <div class="link"><span class="premium"><?php echo $this->translate('PREMIUM');?></span></div>
                        <?php endif; ?>
                        <?php if($contest->endingsoon_id):?>
                        <div class="link"><span class="ending_soon"><?php echo $this->translate('ENDING SOON');?></span></div>
                        <?php endif; ?>
                    <?php endif; ?>
                    </div>
                    <div class="desc_contest">
                        <p><?php echo $this->htmlLink($contest->getHref(),wordwrap(Engine_Api::_()->yncontest()->subPhrase(strip_tags($contest->contest_name),20), 13, "\n", true),array('title'=>$this->string()->stripTags($contest->contest_name))); ?></p>
                    </div>
                    <div class="wrap_desc">
                        <p><?php echo $this->htmlLink($contest->getHref(),wordwrap(Engine_Api::_()->yncontest()->subPhrase(strip_tags($contest->contest_name),20), 13, "\n", true),array('title'=>$this->string()->stripTags($contest->contest_name))); ?></p>
                        <p><span>
                        <?php 
                           if($contest->start_date > date('Y-m-d H:i:s'))
                            {
                                echo $this->locale()->toDateTime( $contest->start_date, array('size' => 'short'));
                            }
                            elseif($contest->end_date < date('Y-m-d H:i:s'))
                            {   
                                echo $this->translate("End");   
                            }   
                            else
                            {
                                if($contest -> yearleft >= 1)
                                    echo $this->translate(array('%s year left','%s years left',$contest -> yearleft),$contest -> yearleft);
                                elseif($contest -> monthleft >= 1)
                                    echo $this->translate(array('%s month left','%s months left',$contest -> monthleft),$contest -> monthleft);
                                    
                                elseif($contest -> dayleft >= 1)
                                    echo $this->translate(array('%s day left','%s days left',$contest -> dayleft),$contest -> dayleft);                           
                                else {                          
                                    echo  $this->translate(array('%s hour %s minute left','%s hours %s minutes left', $contest -> hourleft, $contest -> minuteleft), $contest -> hourleft, $contest -> minuteleft);                                                     
                                }
                            }   
                        ?></span>
                        </p>
                        <p><?php echo $this->translate('Created by')?> <?php echo $owner?></p>
                        <?php if($this->follow):?>
                            <p>
                                <?php                   
                                    echo $this->htmlLink(
                                          array('route' => 'yncontest_mycontest', 'action' => 'un-follow', 'contestId' => $contest->contest_id),
                                          $this->translate('Unfollow'),
                                          array('class' => 'smoothbox buttonlink menu_yncontest_unfollow'));                    
                                 ?>
                            </p>
                        <?php endif;?>
                        <?php if($this->favorite):?>
                            <p>
                                <?php                   
                                    echo $this->htmlLink(
                                          array('route' => 'yncontest_mycontest', 'action' => 'un-favourite', 'contestId' => $contest->contest_id),
                                          $this->translate('Unfavorite'),
                                          array('class' => 'smoothbox buttonlink menu_yncontest_unfavourite'));                 
                                 ?>
                            </p>
                        <?php endif;?>                        
                    </div>
                </div>  
                <div class="yncontest_contest_info">
                    <div class="column">
                        <p><?php echo $this->translate("Participants")?></p>
                        <strong>
                            <?php if($contest->participants==''){
                                    $paticipants = 0;   
                                }                           
                                else {
                                    $paticipants = $contest->participants;
                                }
                                echo $paticipants;
                            ?>
                        </strong>
                    </div>
                    <div class="column">
                        <p><?php echo $this->translate("Entries")?></p>
                        <strong><?php echo $contest->entries;?></strong>
                    </div>
                    <div style="clear:both"></div>
                </div>
                <?php 
                $canRemove = $business->isAllowed('contest_delete', null, $contest);
                $canEdit = $contest->authorization()->isAllowed(null, 'editcontests');
                $canDelete = $contest->authorization()->isAllowed(null, 'deletecontests');
                $canPublish = $contest->approve_status == 'new';
                ?>
                <?php if ($canRemove || $canPublish || $canDelete || $canEdit): ?>
                <div class="ynbusinesspages-profile-module-option ">
                    <?php if ($canEdit): ?>
                        <?php echo $this->htmlLink(
                        array(
                            'action' => 'edit-contest',
                            'contest' => $contest->getIdentity(),
                            'route' => 'yncontest_mycontest',
                            'reset' => true,
                            'business_id' => $this->subject()->getIdentity(),
                            'parent_type' => 'ynbusinesspages_business',
                        ), '<i class="fa fa-pencil-square-o"></i>'.$this->translate('Edit'), array(
                            'class' => 'buttonlink',
                        ))
                        ?>
                    <?php endif; ?>
                    <?php if ($canDelete): ?>
                        <?php echo $this->htmlLink(array(
                            'route' => 'yncontest_mycontest',
                            'action' => 'delete',
                            'contestId' => $contest->getIdentity(),
                            'format' => 'smoothbox',
                            'business_id' => $this->subject()->getIdentity(),
                            'parent_type' => 'ynbusinesspages_business',
                        ),
                        '<i class="fa fa-trash-o"></i>'.$this->translate('Delete'),
                        array('class'=>'buttonlink smoothbox'))
                      ?>
                    <?php endif; ?>
                    <?php if ($canPublish): ?>
                        <?php echo $this->htmlLink(array(
                            'route' => 'yncontest_mycontest',
                            'action' => 'publish',
                            'contest' => $contest->getIdentity(),
                            'format' => 'smoothbox',
                            'view'=> 1,
                            'business_id' => $this->subject()->getIdentity(),
                            'parent_type' => 'ynbusinesspages_business',
                        ),
                        '<i class="fa fa-cloud-upload"></i>'.$this->translate('Publish'),
                        array('class'=>'buttonlink smoothbox'))
                      ?>
                    <?php endif; ?>
                    <?php if ($canRemove): ?>
                    <?php echo $this->htmlLink(array(
                        'route' => 'ynbusinesspages_specific',
                        'action' => 'remove-item',
                        'item_id' => $contest->getIdentity(),
                        'item_type' => 'yncontest_contest',
                        'item_label' => 'Contest',
                        'remove_action' => 'contest_delete',
                        'business_id' => $business->getIdentity(),
                    ),
                    '<i class="fa fa-times"></i>'.$this->translate('Delete Contest To Business'),
                    array('class'=>'buttonlink smoothbox'))
                    ?>
                    <?php endif; ?>  
                </div>
                <?php endif; ?>
                <div style="clear: both"></div> 
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
			  <?php echo $this->translate('No contests have been created.');?>
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
  