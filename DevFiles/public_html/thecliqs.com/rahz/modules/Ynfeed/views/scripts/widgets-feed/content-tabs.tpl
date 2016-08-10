<?php if(count($this->filterTabs)): 
$session = new Zend_Session_Namespace('mobile');
$isMobile = $session -> mobile;?>
<script type="text/javascript">
  var tabYNFeedContainerSwitch = function(element, actionFilter, list_id) 
  {
    if( en4.core.request.isRequestActive())return;
    if( element.tagName.toLowerCase() == 'a' ) {
      element = element.getParent('li');
    }
    var myContainer = element.getParent('.ynfeed_tabs_feed').getParent();
    myContainer.getElements('ul > li').removeClass('ynfeed_tab_active');  
    element.addClass('ynfeed_tab_active'); 
    ynfeedFilter(actionFilter, list_id);
  }
   window.addEvent('domready', function()
   {
	   $('ynfeed_tabs_feed_tab_more').addEvent('click',function()
	   {
	      this.toggleClass('ynfeed_tab_open');
	   });
	});
</script>
<?php $hasAddEditFeed= false; ?>
<div class="ynfeed_tabs_feed ynfeed-clearfix <?php if(!$this->viewer()->getIdentity()):?>ynfeed_tabs_feed_none <?php endif; ?>" id="ynfeed_tabs_feed" <?php if($this->contentTabMax < 1 || count($this->filterTabs)< 2):?> style="background-color: transparent;"<?php endif;?>>
  <ul> 
    <?php foreach ($this->filterTabs as $key => $tab): ?>
      <?php if($tab['filter_type']=='separator'):?>
      	<?php continue; ?>
      <?php endif; ?>
      <?php
      $class = array();
      $class[] = 'tab_' . $key;
      $class[] = 'tab_item_icon_feed_'.$tab['filter_type'] ;
       if( $this->actionFilter == $tab['filter_type'] )
          $class[] = 'ynfeed_tab_active';
      $class = join(' ', $class);?>    
      <?php if ($key < $this->contentTabMax): ?>
        <li id="tab_ynFeed_<?php echo $tab['filter_type'] ?>" class="<?php echo $class ?>">  
          <a href="javascript:void(0);" onclick="tabYNFeedContainerSwitch($(this), '<?php echo $tab['filter_type'] ?>','<?php echo $tab['list_id'] ?>');"  ><?php echo $this->translate($tab['tab_title']) ?>
            <?php if($tab['filter_type']=='all') : ?>
            	<span id="update_advfeed_blink" class="notification_star"></span>
            <?php endif; ?>
          </a>
          <?php if($tab['filter_type']=='custom_list'):?>
              <span class="ynfeed_<?php echo $tab['filter_type'] ?>_icon">
                <a href="<?php echo $this->url(array('controller'=>'custom-list','action' => 'edit','list_id'=>$tab['list_id']),'ynfeed_extended') ?>"  class="smoothbox edit_custom_list_icon" title="<?php echo $this->translate("Edit this List") ?>"></a>
              </span>
        	<?php endif; ?>
        </li>
      <?php else:?>            
      <?php break; ?>        
      <?php endif; ?>

    <?php endforeach; ?>   
    <?php if (count($this->filterTabs) > $this->contentTabMax || $this->canCreateCustomList): ?>
      <li class="ynfeed_tabs_feed_tab ynfeed_tabs_feed_tab_more" id="ynfeed_tabs_feed_tab_more" >
        <div class="ynfeed_pulldown_contents_wrapper">
          <div class="ynfeed_pulldown_contents">
            <ul>
              <?php foreach ($this->filterTabs as $key => $tab): ?>
              <?php if($tab['filter_type']=='separator'):?>
               <li class="sep"></li>
              <?php else: ?>
              <?php 
                $class = array();
                $class[] = 'tab_' . $key;
                if (strpos($tab['filter_type'], '_listtype_') !== false) 
                {
                	$class[] = 'item_icon_sitereview_listing' ;
                }
                $class[] = 'item_icon_'.str_replace('yn', '', $tab['filter_type']) ; 
                if(isset($tab['list_id'])&& !empty($tab['list_id']))
                	$class[] = 'item_icon_'.$tab['filter_type'].'_'.$tab['list_id'] ; 
                if( $this->actionFilter == $tab['filter_type'] )
                	$class[] = 'ynfeed_tab_active';
                $class = join(' ', array_filter($class));
                ?>
              <?php if ($key >= $this->contentTabMax): ?>
                <li id="tab_ynFeed_<?php echo $tab['filter_type'] ?>" class="ynfeed_custom_list <?php if( $this->actionFilter == $tab['filter_type'] ): ?>ynfeed_tab_active<?php endif; ?>" onclick="tabYNFeedContainerSwitch($(this), '<?php echo $tab['filter_type'] ?>','<?php echo $tab['list_id'] ?>')">
                  <i class="<?php echo $class ?> ynfeed_content_list_icon" <?php if($tab['icon_url']):?> style = "background-image:url(<?php echo $tab['icon_url']?>)" <?php endif;?>></i>
                	<?php if($tab['filter_type']=='custom_list'):?>
	                      <span class="ynfeed_<?php echo $tab['filter_type'] ?>_icon">
	                        <a href="<?php echo $this->url(array('controller'=>'custom-list','action' => 'edit','list_id'=>$tab['list_id']),'ynfeed_extended') ?>"  class="<?php if(!$isMobile) echo 'smoothbox';?> edit_custom_list_icon" title="<?php echo $this->translate("Edit this List") ?>"></a>
	                      </span>
                    	<?php endif; ?>
                	<div><?php echo $this->translate($tab['tab_title']) ?></div>
                </li>                
              <?php endif; ?>
              <?php endif; ?>                
              <?php endforeach; ?>
              <?php if($this->viewer()->getIdentity()): ?>
              	<?php if(count($this->filterTabs) > $this->contentTabMax):?>
              		 </ul>
             		 <ul>
                 	<li class="ynfeed-separator"></li>
                 <?php endif;?>
                 
                 <!-- custom filter -->
                 <?php if($this->canCreateCustomList):?>
	                <li id="" class="ynfeed_custom_list_link">
	                  <a href="<?php echo $this->url(array('controller'=>'custom-list','action' => 'create'),'ynfeed_extended') ?>" class="<?php if(!$isMobile) echo 'smoothbox';?> ynfeed_icon_feed_create">
	                    <?php echo $this->translate("Create a List") ?>
	                  </a>
	                </li>
                <?php endif;?>
                
                <!-- settings (manage hidden users)-->
                <li id="" class="ynfeed_custom_list_link">
                   <a href="<?php echo $this->url(array('controller'=>'index','action' => 'edit-hide-options'),'ynfeed_extended') ?>" class="smoothbox ynfeed_icon_feed_settings">
                     <?php echo $this->translate("Settings") ?>
                  </a>                 
                </li>              
              <?php endif;?>
              </ul>
          </div>
        </div>
        <a href="javascript:void(0);"><span><?php echo count($this->filterTabs)>0 && $this->contentTabMax !=0 ?$this->translate('More'):count($this->filterTabs)<1?$this->translate('More'):$this->translate('Filter') ?></span>&nbsp;<i class="fa fa-caret-down"></i></a>
      </li>
    <?php endif; ?>
  </ul>
</div>
<?php endif; ?>