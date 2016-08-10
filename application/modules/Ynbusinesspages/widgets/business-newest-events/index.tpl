<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
  <ul id="ynbusinesspages_newest_events">
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
                    <!--<span class="rating" title="<?php echo $this -> translate("Rates")?>"><?php echo number_format($event->rating, 1);?> <i class="ynicon-rating-w<?php if ($event->rating==0) echo "gray";?>"></i></span>-->
                <?php endif;?>
            </div>                
          </div>
          <div class="desc"><?php echo strip_tags($this -> string() -> truncate($event->description, 250));?></div>
      </li>
    <?php endforeach;?>
  </ul>
<?php endif; ?>
