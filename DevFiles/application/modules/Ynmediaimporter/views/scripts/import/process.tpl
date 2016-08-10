<script type="text/javascript">    
    function processDone(message){
       $('message_stage_popup_ajax').innerHTML = message;
       function callback(){
           parent.YnMediaImporter.refresh();
           parent.Smoothbox.close();
       }
       window.setTimeout(callback,3000);
    }

	function closePopup(){
		alert('Your request has been added to the queue.');
		parent.YnMediaImporter.refresh();
        parent.Smoothbox.close();
	}
    
	var request = new Request.JSON({
        url: en4.core.baseUrl + '?m=lite&module=ynmediaimporter&name=schedule&scheduler_id=<?php echo $this -> scheduler_id; ?>',
    	method: 'post',
    	onError: function(){
    	    processDone('<?php echo $this->string()->escapeJavascript($this->translate('Your request has been added to the queue.'))?>');
    	},
    	onSuccess: function(json, text)
    	{
    	    processDone(json.message);
    	}
	});
	
	request.send();
	window.setTimeout(closePopup, 12000);
		
</script>
<div class="ynmediaimporter_loading_image" style="display:block;">
</div>
<div>
    <center id="message_stage_popup_ajax">
      <?php echo $this->translate("Processing your request ...");?>    
    </center>
</div>
<div style="width:400px;">&nbsp;</div>