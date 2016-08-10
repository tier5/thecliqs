<?php
$this->headScript()
       ->appendFile($this->baseUrl() . '/application/modules/Mp3music/externals/scripts/music_function.js');   
       ?> 
 <script type="text/javascript">
 //<![CDATA[
 var search_params = <?php echo Zend_Json::encode($this->params) ?> ;
  function do_search() 
  {
        var key_search = $("title").value;
        key_search = key_search.replace(/^\s+|\s+$/g,'');
        if (key_search == "")
        {
            alert ('<?php echo $this->translate("Please enter keyword!") ?>');
            $("title").focus();
            return false;
        }
        else
        {
            $('frm_search').submit();
            return true;
        }
  }
  function active_search(s_name)
  {
	    $("search_all").className=$("search_songs").className=$("search_singer").className=$("search_artist").className=$("search_playlists").className=$("search_album").className=$("search_album").className="";
	    $("search_" + s_name).className="mp3_current";
	    if(s_name == "artist")
	    {   
	        <?php $allow_artist = Engine_Api::_()->getApi('settings', 'core')->getSetting('mp3music.artist', 1);
	         if($allow_artist):?>
	            s_name = "owner";
	        <?php endif;?>
	    }   
    	$("type_search").value=s_name;
    }
    //]]>
</script>             
<ul class="global_form_box" style="margin-bottom: 15px;">
    <div>
        <ul class="solidblockmenu" style="background: none;">
            <li><a id="search_all" href="javascript:void(0);" onClick="active_search('all');" <?php if($this->params['search'] == "all"): echo 'class="mp3_current"'; endif; ?>><?php echo $this->translate('All'); ?> </a></li>
            <li><a id="search_songs" href="javascript:void(0);" onClick="active_search('songs');" <?php if($this->params['search'] == "songs" || $this->params['search'] == ""): echo 'class="mp3_current"'; endif; ?>><?php echo $this->translate('Song'); ?></a></li>
            <li><a id="search_album" href="javascript:void(0);" onClick="active_search('album');" <?php if($this->params['search'] == "album"): echo 'class="mp3_current"'; endif; ?>><?php echo $this->translate('Album'); ?></a></li>
            <li><a id="search_singer" href="javascript:void(0);" onClick="active_search('singer');" <?php if($this->params['search'] == "singer"): echo 'class="mp3_current"'; endif; ?>><?php echo $this->translate('Singer'); ?> </a></li>
            <li><a id="search_artist" href="javascript:void(0);" onClick="active_search('artist');" <?php if($this->params['search'] == "artist" || $this->params['search'] == "owner"): echo 'class="mp3_current"'; endif; ?>><?php echo $this->translate('Artist'); ?> </a></li>                        
            <li><a id="search_playlists" href="javascript:void(0);" onClick="active_search('playlists');" <?php if($this->params['search'] == "playlists"): echo 'class="mp3_current"'; endif; ?>><?php echo $this->translate('Playlist'); ?> </a></li>
        </ul>                    
      </div> 
    <div>
       <form name="frm_search" id = "frm_search" method="get" onsubmit="return do_search();" action ="<?php echo $this->url(array(),'default') ?>mp3-music">
           <input style="width: 78%; margin-right: 10px" type="text" id="title"  name="title" value="<?php echo $this->params['title'];?>" class="box_search_music" onkeydown="javascript:getKeyDown(event);"/>
           <input type="hidden" id="type_search" name="search" value="<?php echo $this->params['search'];?>"/>
       </form>
       <button name=""  onclick="return do_search();"><?php echo $this->translate('Search'); ?> </button>
    </div> 
    <div >
    </div>
</ul>