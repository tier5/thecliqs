<script type="text/javascript">
  en4.core.runonce.add(function(){
    <?php if( !$this->renderOne ): ?>
    var anchor = $('ynbusinesspages_profile_events').getParent();
    $('ynbusinesspages_profile_events_previous').style.display = '<?php echo ( $this->paginator->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>';
    $('ynbusinesspages_profile_events_next').style.display = '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' ) ?>';

    $('ynbusinesspages_profile_events_previous').removeEvents('click').addEvent('click', function(){
      en4.core.request.send(new Request.HTML({
        url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
        data : {
          format : 'html',
          subject : en4.core.subject.guid,
          page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() - 1) ?>
        }
      }), {
        'element' : anchor
      })
    });

    $('ynbusinesspages_profile_events_next').removeEvents('click').addEvent('click', function(){
      en4.core.request.send(new Request.HTML({
        url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
        data : {
          format : 'html',
          subject : en4.core.subject.guid,
          page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>
        }
      }), {
        'element' : anchor
      })
    });
    <?php endif; ?>
  });
</script>

<div class="ynbusinesspages-profile-module-header">
    <div class="ynbusinesspages-profile-header-right">
    <?php
    	if(count($this->paginator) > 0)
    	{
    		echo $this->htmlLink(array(
    	          'route' => 'ynbusinesspages_extended',
    	          'controller' => 'event',
    	          'action' => 'list',
    	          'subject' => $this->subject()->getGuid(),
    	          'tab' => $this->identity,
    	        ), '<i class="fa fa-list"></i>'.$this->translate('View All Events'), array(
    	          'class' => 'buttonlink'
    	      ));
    	}

      if( $this->canAdd ): ?>
        <?php echo $this->htmlLink(array(
            'route' => 'event_general',
            'action' => 'create',
            'parent_type'=> 'ynbusinesspages_business',
            'subject_id' => $this->subject()->getIdentity(),
          ), '<i class="fa fa-plus-square"></i>'.$this->translate('Add Event'), array(
            'class' => 'buttonlink'
        )) ?>
      <?php endif; ?>
  </div>
  <div class="ynbusinesspages-profile-header-content">
        <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
            <span class="ynbusinesspages-numeric"><?php echo $this->paginator->getTotalItemCount(); ?></span> 
            <?php echo $this-> translate(array("ynbusiness_event", "Events", $this->paginator->getTotalItemCount()), $this->paginator->getTotalItemCount());?>
        <?php endif; ?>
    </div>
</div>

<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
  <ul id="ynbusinesspages_profile_events">
    <?php foreach( $this->paginator as $event ): ?>
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
          <div class="desc"><?php echo strip_tags($this -> string() -> truncate($event->description, 250));?></div>
      </li>
    <?php endforeach;?>
  </ul>

    <div class="ynbusinesspages-paginator">
      <div id="ynbusinesspages_profile_events_previous" class="paginator_previous">
        <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
          'onclick' => '',
          'class' => 'buttonlink icon_previous'
        )); ?>
      </div>
      <div id="ynbusinesspages_profile_events_next" class="paginator_next">
        <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
          'onclick' => '',
          'class' => 'buttonlink_right icon_next'
        )); ?>
      </div>
    </div>

<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('No events have been added to this business yet.');?>
    </span>
  </div>

<?php endif; ?>
