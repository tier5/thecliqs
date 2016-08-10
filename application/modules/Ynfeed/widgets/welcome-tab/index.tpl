<?php
$content = $this -> content;
?>
<!-- Message Block -->
<div class="ynfeed_welcome_message">
	<div class="ynfeed_message_icon"></div>
	<div class="ynfeed_message_info">
		<div class="ynfeed_message_title"><?php echo $content -> title;?></div>
		<div class="ynfeed_message_content"><?php echo $content -> body;?></div>
	</div>
</div>

<!-- Contact Importer Block -->
<?php if(Engine_Api::_() -> hasModuleBootstrap('contactimporter') && $content -> enabled_contact):?>
<div class="ynfeed_welcome_sep"></div>
<div class="ynfeed_welcome_contact">
	<div class="ynfeed_contact_icon"></div>
	<div class="ynfeed_contact_info">
		<div class="ynfeed_contact_title">
			<?php echo $this -> translate("Access to address books");?>
		</div>
		<div class="ynfeed_contact_description">
			<?php echo $this -> translate("to invite friends to connect with them in the social networking");?>
		</div>
		<div class="ynfeed_contact_content">
			<?php echo $this->content()->renderWidget('contactimporter.homepage-inviter'); ?>
		</div>
	</div>
</div>
<?php endif;?>

<!-- Friend Reuqests Block -->
<?php if(count($this -> friend_requests) && $content -> enabled_friend):?>
<div class="ynfeed_welcome_sep"></div>
<div class="ynfeed_welcome_toggle">
	<span class="ynfeed_welcome_toggle_icon"><i class="fa fa-chevron-down"></i></span>
	<div class="ynfeed_welcome_heading ynfeed_welcome_friend_requests">
		<?php echo $this -> translate("Accept your Friend Requests")?>
	</div>
</div>
<div class="ynfeed_welcome_requests ynfeed-clearfix" id="ynfeed_welcome_requests">
</div>
<?php endif;?>

<!-- Search Friends Block -->
<?php if($content -> enabled_search_fr):?>
<div class="ynfeed_welcome_sep"></div>
<div class="ynfeed_welcome_search">
	<div class="ynfeed_search_icon"></div>
	<div class="ynfeed_search_info">
		<div class="ynfeed_welcome_search_heading">
			<?php 
			$title = Engine_Api::_()->getApi('settings', 'core')->getSetting('core_general_site_title', $this->translate('_SITE_TITLE'));
			$route = $this->viewer()->getIdentity()
	             ? array('route'=>'user_general', 'action'=>'home')
	             : array('route'=>'default');
			echo $this -> translate("Search friends already on %s", $this->htmlLink($route, $title))?>
		</div>
		<form action="<?php echo $this ->url(array(), 'user_general', true)?>" method = "post">
			<input class="ynfeed_welcome_search_input" type="text"  name="displayname"/>
			<div class="ynfeed_welcome_search_button">
				<button type="submit" name="extra[done]" id="extra-done" type="button"><?php echo $this -> translate("Find Friends")?></button>
			</div>
		</form>
	</div>	
</div>
<?php endif;?>

<!-- Member Suggestions Block -->
<?php if(count($this -> member_suggestions) && $content -> enabled_member_sug):?>
<div class="ynfeed_welcome_sep"></div>
<div class="ynfeed_welcome_toggle">
	<span class="ynfeed_welcome_toggle_icon"><i class="fa fa-chevron-down"></i></span>
	<div class="ynfeed_welcome_heading ynfeed_welcome_member_suggestions">
		<?php echo $this -> translate("Member Suggestions")?>
	</div>
</div>
<div class="ynfeed_welcome_member_sug ynfeed-clearfix" id="ynfeed_welcome_member_sug">
</div>
<?php endif;?>

<!-- Group Suggestions Block -->
<?php if(count($this -> group_suggestions) && $content -> enabled_group_sug):?>
<div class="ynfeed_welcome_sep"></div>
<div class="ynfeed_welcome_toggle">
	<span class="ynfeed_welcome_toggle_icon"><i class="fa fa-chevron-down"></i></span>
	<div class="ynfeed_welcome_heading ynfeed_welcome_group_suggestions">
		<?php echo $this -> translate("Group Suggestions")?>
	</div>
</div>
<div class="ynfeed_welcome_group_sug ynfeed-clearfix" id="ynfeed_welcome_group_sug">
</div>
<?php endif;?>

<!-- Event Suggestion Block -->
<?php if(count($this -> event_suggestions) && $content -> enabled_event_sug):?>
<div class="ynfeed_welcome_sep"></div>
<div class="ynfeed_welcome_toggle">
	<span class="ynfeed_welcome_toggle_icon"><i class="fa fa-chevron-down"></i></span>
	<div class="ynfeed_welcome_heading ynfeed_welcome_event_suggestions">
		<?php echo $this -> translate("Event Suggestions")?>
	</div>
</div>
<div class="ynfeed_welcome_event_sug ynfeed-clearfix" id="ynfeed_welcome_event_sug">
</div>
<?php endif;?>

<!-- Most Liked Items Block -->
<?php if(count($this -> most_liked_items) && $content -> enabled_most_like):?>
<div class="ynfeed_welcome_sep"></div>
<div class="ynfeed_welcome_toggle">
	<span class="ynfeed_welcome_toggle_icon"><i class="fa fa-chevron-down"></i></span>
	<div class="ynfeed_welcome_heading ynfeed_welcome_most_liked">
		<?php echo $this -> translate("Most Liked Items")?>
	</div>
</div>
<div class="ynfeed_welcome_liked_sug ynfeed-clearfix" id="ynfeed_welcome_liked_sug">
	<ul>
		<?php foreach($this -> most_liked_items as $item): 
		if(Engine_Api::_() -> hasItemType($item -> resource_type)):
		$resource = Engine_Api::_() -> getItem($item -> resource_type, $item -> resource_id);
		if($resource && is_object($resource)):?>
		<li class="ynfeed_user_item">
			<?php if(!in_array($resource -> getShortType(), array('blog','poll'))):?>
				<a href="<?php echo $resource->getHref(); ?>">
				    <?php echo $this->itemPhoto($resource, 'thumb.profile');?>
				</a>
			<?php else:?>
				<a href="<?php echo $resource -> getOwner() ->getHref(); ?>">
				    <?php echo $this->itemPhoto($resource -> getOwner(), 'thumb.profile');?>
				</a>
			<?php endif;?>
			<div class="ynfeed_liked_info">
				<div class="ynfeed_liked_link">
					<?php echo $resource;?>
				</div>
	    		<div class="ynfeed_liked_author"> 
	    			<?php echo $this->translate("Posted by %s", $resource -> getOwner());?>
	    		</div>
	    		<div class="ynfeed_liked_stats"> 
	    			<span class="ynfeed_liked_type" title="<?php echo $this -> translate($resource -> getShortType())?>"> <i class="ynfeed_liked_type_icon item_icon_<?php echo $resource -> getShortType()?>"></i><?php echo $this -> translate(ucfirst($resource -> getShortType())); ?></span>
	    		</div>
	    		<div class="ynfeed_liked_stats">
	    			<span class="ynfeed_liked_count" title="<?php echo $this -> translate("Liked")?>"> <i class="ynfeed_icon_like"></i><?php echo $this -> translate(array("%s like", "%s likes", $item->count), $item->count); ?></span>
	    		</div>
	    	</div>
	    </li>
		<?php endif; endif; endforeach;?>
	</ul>
</div>
<?php endif;?>

<script type="text/javascript">
window.addEvent('domready', function()
{
    $$('.ynfeed_welcome_toggle_icon').addEvent('click', function(){
        this.toggleClass('toggle-open');
        this.getParent().getNext().toggle();
    });
    var img_loading = '<?php echo $this->baseUrl(); ?>/application/modules/Ynfeed/externals/images/loading.gif';
   
   <?php if(count($this -> friend_requests) && $content -> enabled_friend):?>
      $('ynfeed_welcome_requests').innerHTML = '<center><img src="'+ img_loading +'" border="0" /></center>';
      loadFriendRequests();
   <?php endif;?>
   
   <?php if(count($this -> member_suggestions) && $content -> enabled_member_sug):?>
      $('ynfeed_welcome_member_sug').innerHTML = '<center><img src="'+ img_loading +'" border="0" /></center>';
      loadMemberSuggestions();
   <?php endif;?>
   
   <?php if(count($this -> group_suggestions) && $content -> enabled_group_sug):?>
      $('ynfeed_welcome_group_sug').innerHTML = '<center><img src="'+ img_loading +'" border="0" /></center>';
      loadGroupSuggestions(0);
   <?php endif;?>
   
   <?php if(count($this -> event_suggestions) && $content -> enabled_event_sug):?>
      $('ynfeed_welcome_event_sug').innerHTML = '<center><img src="'+ img_loading +'" border="0" /></center>';
      loadEventSuggestions(0);
   <?php endif;?>
});
var loadFriendRequests = function()
{
      new Request.HTML({
       'url'    :    en4.core.baseUrl + 'ynfeed/welcome/friend-requests/',
       'data' : {
            'format' : 'html',
            'limit' : '<?php echo $content -> number_of_friend?>'
        },
        'onComplete' : function(responseTree, responseElements, responseHTML, responseJavaScript) 
        {
            if(responseHTML)
            { 
                $('ynfeed_welcome_requests').innerHTML = responseHTML;
            }
        }
       }).send();
}

var loadMemberSuggestions = function()
{
      new Request.HTML({
       'url'    :    en4.core.baseUrl + 'ynfeed/welcome/member-suggestions/',
       'data' : {
            'format' : 'html',
            'limit' : '<?php echo $content -> number_of_member?>'
        },
        'onComplete' : function(responseTree, responseElements, responseHTML, responseJavaScript) 
        {
            if(responseHTML)
            { 
                $('ynfeed_welcome_member_sug').innerHTML = responseHTML;
            }
        }
       }).send();
}

var loadGroupSuggestions = function(category)
{
      new Request.HTML({
       'url'    :    en4.core.baseUrl + 'ynfeed/welcome/group-suggestions/',
       'data' : {
            'format' : 'html',
            'limit' : '<?php echo $content -> number_of_group?>',
            'category' : category
        },
        'onComplete' : function(responseTree, responseElements, responseHTML, responseJavaScript) 
        {
            if(responseHTML)
            { 
                $('ynfeed_welcome_group_sug').innerHTML = responseHTML;
            }
        }
       }).send();
}

var loadEventSuggestions = function(category)
{
      new Request.HTML({
       'url'    :    en4.core.baseUrl + 'ynfeed/welcome/event-suggestions/',
       'data' : {
            'format' : 'html',
            'limit' : '<?php echo $content -> number_of_event?>',
            'category' : category
        },
        'onComplete' : function(responseTree, responseElements, responseHTML, responseJavaScript) 
        {
            if(responseHTML)
            { 
                $('ynfeed_welcome_event_sug').innerHTML = responseHTML;
            }
        }
       }).send();
}
</script>