<?php 	 	
  	//echo $this->partial('_list_entries_video.tpl', array('paginator'=>$paginator, 'recentType'=>$recentType, 'recentCol'
  	// =>$recentCol ));
  	
  	echo $this->partial(Yncontest_Api_Core::partialViewFullPath('_list_entries.tpl'), array(
  			'paginator' => $this->paginator,
  			'recentType' => $this->recentType,
  			'recentCol' => $this->recentCol
  	));
?>