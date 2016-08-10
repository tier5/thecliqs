<?php if(!$this -> ajax):?>
<div class="layout_ynfeed_feed ynfeed_view_shared">
	<h3><?php echo $this -> translate("People Who Shared This")?></h3>
	<div id="ynfeed_shared_actions">
<?php endif;?>
		<?php echo $this->ynfeedLoop($this->actions, array(
		  'action_id' => 0,
		  'notShowActions' => true,
		  'viewAllComments' => (bool)($this->count == 1),
		  'viewAllLikes' => (bool)($this->count == 1),
		  'getUpdate' => false,
		)) ?>
<?php if(!$this -> ajax):?>
	</div>
</div>
<input type="hidden"  name="page" id='page' value="1"/>  
<?php endif;?>
<?php if (!empty($this->count)): ?>
<div id="view_more_sea" class="clr"  style="display:<?php echo ( $this->actions->count() == $this->actions->getCurrentPageNumber() ? 'none' : '' ) ?>">
  <div id="view_more_link" onclick="getNextPage()" class="ynfeed_item_list_popup_more">
  	<a href="javascript:void(0);" class="more_icon buttonlink"><?php echo $this->translate('More'); ?></a>
  </div>
  <div id="view_more_loding" style="display:none" class="ynfeed_item_list_popup_more">
    <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Ynfeed/externals/images/loading.gif' />
  </div>
</div>
<?php endif; ?>
<?php if(!$this -> ajax):?>
<script type="text/javascript">
	function getNextPage()
	{
	  document.getElementById('page').value=parseInt(document.getElementById('page').value) + 1;
	  getContentItem();
	  if(document.getElementById('view_more_sea'))
	  {
	      document.getElementById('view_more_link').style.display ='none';
	      document.getElementById('view_more_loding').style.display ='';  
	  }
	}
	var getContentItem = function()
	{ 
	    var url = '<?php echo $this->url(array('module' => 'ynfeed', 'controller' => 'index', 'action' => 'view-shared'), 'default', true) ?>';
	    var request = new Request.HTML({
	      url : url,
	      data : {
	        format : 'html',
	        'page': document.getElementById('page').value,
	        'id' : '<?php echo $this -> action_id?>',
	        'ajax': true
	      },
	      evalScripts : true,
	      onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
	        if(document.getElementById('view_more_sea'))
	        {
	        	document.getElementById('view_more_sea').destroy();       
	        }
	        Elements.from(responseHTML).inject(document.getElementById('ynfeed_shared_actions'));
	        $('global_page_ynfeed-index-view-shared').getElements('ul > li a').setProperty('target', '_top');        
	      }
	    });
	    request.send();
  	}
  	function ynfeedFilter(filter_type, filter_id)
	{
		parent.ynfeedFilter(filter_type, filter_id);
		parent.Smoothbox.close();
	}
	$('global_page_ynfeed-index-view-shared').getElements('ul > li a').setProperty('target', '_top');
</script>
<?php endif; ?>