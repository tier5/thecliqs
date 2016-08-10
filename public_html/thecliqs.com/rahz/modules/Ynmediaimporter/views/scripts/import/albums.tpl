<style type="text/css">
    #ynmediaimporter_json_data {
        display: none;
    }
</style>
<script type="text/javascript">
    function do_cancel(){
       parent.Smoothbox.close();
    }
    function do_callback(json) {
        if (json.album_count) {
            // alert('album count' + json.album_count);
            $('form-check').action = en4.core.baseUrl + 'ynmediaimporter/import/albums/format/smoothbox';
        } else if (json.photo_count) {
            // alert('photo count' + json.photo_count);
        } else {
            window.setTimeout(do_cancel, 3000);
        }
        $('ynmediaimporter_json_data').value =  JSON.encode(json);
        $('message_stage').innerHTML = json.message;
        $('message_stage').style.display = 'block';
    }
</script>
<?php echo isset($this -> form) ? $this -> form -> render($this) : ''; ?>
