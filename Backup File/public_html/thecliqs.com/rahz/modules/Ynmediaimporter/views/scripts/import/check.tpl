<style type="text/css">
	#search-wrapper, #auth_view-wrapper, #auth_comment-wrapper, #auth_tag-wrapper,#ynmediaimporter_json_data,#buttons-wrapper {
		display: none;
	}
	#message_stage{
	    padding: 5px 0;
	}
	#form-check{height:200px;}
</style>
<script type="text/javascript">
	var rows = parent.YnMediaImporter.getSelected();
	function do_cancel(){
	   parent.Smoothbox.close();
	}
	function do_callback(json) {
	    $('buttons-wrapper').style.display = 'block';
	    if(json.numphoto==0){
	        window.setTimeout(do_cancel,3000 );
	        $('buttons-wrapper').style.display = 'none';
	    }else if (json.album_count) {
			// alert('album count' + json.album_count);
			$('form-check').action = en4.core.baseUrl + 'ynmediaimporter/import/albums/format/smoothbox';
		} else if (json.photo_count) {
			// alert('photo count' + json.photo_count);
		} else {
		    $('buttons-wrapper').style.display = 'none';
			window.setTimeout(do_cancel, 3000);
		}
		$('ynmediaimporter_json_data').value =  JSON.encode(json);
		$('message_stage').innerHTML = json.message;
		$('message_stage').style.display = 'block';
	}
	// post silent data
	var request = new Request.JSON({
		url : en4.core.baseUrl + '?m=lite&module=ynmediaimporter&name=postimport',
		method : 'post',
		data : {
			json : JSON.encode(rows)
		},
		onSuccess : do_callback
	});
	request.send(); 
</script>
<?php echo isset($this -> form) ? $this -> form -> render($this) : ''; ?>
