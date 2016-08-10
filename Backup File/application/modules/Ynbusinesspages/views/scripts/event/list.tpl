<!-- Header -->
<div class="generic_layout_container layout_top">
  <div class="generic_layout_container layout_middle">
    <div class="headline">
		<h2>
			<?php echo $this->business->__toString()." ";
				echo $this->translate('&#187; Events');
			?>
		</h2>
	</div>
	</div>
</div>

<div class="generic_layout_container layout_main ynbusinesspages_event_list">
	<div class="generic_layout_container layout_right">
		<!-- Search Form -->
		<div class="ynbusinesspages_event_search_form">
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

		            <?php echo $this->htmlLink(array('route' => 'ynbusinesspages_extended', 'controller'=>'event','action'=>'manage','subject' => $this->subject()->getGuid()), '<i class="fa fa-user"></i>'.$this->translate('My Events'), array(
						'class' => 'buttonlink'
					)) ?>

					<?php if( $this->canCreate ): ?>
						<?php echo $this->htmlLink(array(
							'route' => 'event_general',
							'action' => 'create',
							'parent_type' =>'ynbusinesspages_business',
							'subject_id' =>  $this->business->business_id,
						  ), '<i class="fa fa-plus-square"></i>'.$this->translate('Create New Event'), array(
							'class' => 'buttonlink'
						)) ?>
					<?php endif; ?>
	            </div>
	        </div>

			<!-- Content -->
			<?php if ($this->paginator->getTotalItemCount()> 0) : ?>
	   		<ul class='ynbusinesspages_event_browse'>
	        <?php foreach ($this->paginator as $event): ?>
				<li class="ynbusinesspages_profile_event_item">
			        <div class="photo">
			            <?php echo Engine_Api::_()->ynbusinesspages()->getPhotoSpan($event, 'thumb.normal'); ?>
			          </div>
			          <div class="info">
			            <div class="date">
			              <span class="day"><?php 
			                $start_time = strtotime($event -> starttime);
			                $oldTz = date_default_timezone_get();
			                if($this->viewer() && $this->viewer()->getIdentity())
			                {
			                  date_default_timezone_set($this -> viewer() -> timezone);
			                }
			                else {
			                  date_default_timezone_set( $this->locale() -> getTimezone());
			                }
			                echo date("d", $start_time); ?>
			              </span>
			              <span class="month">
			                <?php echo date("M", $start_time); 
			                  date_default_timezone_set($oldTz);?>
			              </span>
			            </div>
			            <div class="title">
                        <div><?php echo $this->htmlLink($event->getHref(), $this -> string() -> truncate($event->getTitle(), 50)) ?></div>
			             <span class="events_members" style="font-weight: normal">
                           <?php 
                                if($event->host)
                                {
                                    if(strpos($event->host,'younetco_event_key_') !== FALSE)
                                    {
                                        $user_id = substr($event->host, 19, strlen($event->host));
                                        $user = Engine_Api::_() -> getItem('user', $user_id);
                                        
                                        echo $this->translate('host by %1$s',
                                        $this->htmlLink($user->getHref(), $user->getTitle())) ;
                                    }
                                    else
                                    {
                                        echo $this->translate('host by %1$s', $event->host);
                                    }
                                }
                                else
                                {
                                    $owner = Engine_Api::_() -> getDbTable('mappings', 'ynbusinesspages') -> getOwner($event);
                                    echo $this->translate('by %1$s',
                                        $this->htmlLink($event->getOwner()->getHref(), $this -> string() -> truncate($owner -> getTitle(), 25)));
                                }
                                ?>
                          </span>
                        </div> 
                        <div class="stats">
                            <span class="person" title="<?php echo $this -> translate("Guests")?>"><?php echo $event->member_count; ?> <i class="ynicon-person"></i></span>
                            <span class="view" title="<?php echo $this -> translate("Views")?>"><?php echo $event->view_count; ?> <i class="ynicon-followed"></i></span>
                            <?php if(Engine_Api::_() -> hasModuleBootstrap('ynevent')):?>
                                <span class="like" title="<?php echo $this -> translate("Likes")?>"><?php echo $event->likes()->getLikeCount(); ?> <i class="ynicon-liked-m<?php if ($event->likes()->getLikeCount()==0) echo "gray";?>"></i></span>
                                <span class="rating" title="<?php echo $this -> translate("Rates")?>"><?php echo number_format($event->rating, 1);?> <i class="ynicon-rating-w<?php if ($event->rating==0) echo "gray";?>"></i></span>
                            <?php endif;?>
                        </div>                          
			          </div>
			          <div class="desc"><?php echo $this -> string() -> truncate($event->description, 250);?></div>
			      </li>
	        <?php endforeach; ?>
	    	</ul>

			<div class ="ynbusinesspages_event_pages">
				<?php echo $this->paginationControl($this->paginator, null, null, array(
					'pageAsQuery' => true,
					'query' => $this->formValues,
				)); ?>
			</div>
	      
			<?php else : ?>
			<div class="tip">
				<span>
					<?php echo $this->translate('There is no event found.'); ?>
				</span>
			</div>
			<?php endif; ?>
		</div>
	</div>
</div>
<script type="text/javascript">
  en4.core.runonce.add(function()
  {
   if($('text'))
    {
      new OverText($('text'), {
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