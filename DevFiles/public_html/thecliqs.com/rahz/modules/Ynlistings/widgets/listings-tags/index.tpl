<?php if(count($this->tags) > 0):?>
<ul class = "global_form_box"  style="margin-bottom: 15px;">
<span id="script" style="font-size: 9pt;">
     <?php
            $index = 0;
            $flag = false;
            foreach($this->tags as $tag):
              $index ++;
              if(trim($tag->text) != ""):
              if($index > 25 && $flag == false): $flag = true;?>
                  <p id="showlink" style="display: block; font-weight: bold">
                    [
                    <a id = 'title' href="#" onclick="showhide('hide'); return(false);"><?php echo $this->translate('show all');?></a>
                    ]
                  </p>
</span>
  <span id="hide" style="display:none;font-size: 8pt;">
                      <?php  endif;?>
             <span style="<?php if($tag->count > 99 && $tag->count < 599): echo "font-size:".($tag->count/80 + 8)."pt"; elseif($tag->count > 599): echo "font-size: 14pt"; endif; ?>">
          <a  href='javascript:void(0);'onclick='javascript:tagAction(<?php echo $tag->tag_id; ?>);' ><?php echo $tag->text?></a> (<?php echo $tag->count?>)
          </span>
             <?php endif; endforeach;
             if($flag == true):?>
             <p id="hidelink" style="display: none;font-weight: bold">[<a id = 'title' href="#" onclick="showhide('hide'); return(false);"><?php echo $this->translate('hide');?></a>]</p>
             <?php endif; ?>
</span>
 </ul>
<?php endif;?>
<form id="tag_form" display="none" method="post">
   <input type="hidden" id="tag_filter" name="tag"/>
   <?php if ($this->user) : ?>
   <input type="hidden" name="user_id" value="<?php echo $this->user?>"/>
   <?php endif; ?>
</form>
<script type="text/javascript">
  var tagAction =function(tag){
    var url = en4.core.baseUrl+'listings/browse';
    $('tag_filter').value = tag;
    $('tag_form').set('action', url);
    $('tag_form').submit();
}

  function showhide(id)
{
    if (document.getElementById)
    {
        obj = document.getElementById(id);
        if (obj) {
            if (obj.style.display == "none")
            {
                obj.style.display = "";
                $('showlink').style.display = "none";
                $('hidelink').style.display = "";
            } else
            {
                obj.style.display = "none";
                 $('showlink').style.display = "";
                $('hidelink').style.display = "none";
            }
        }
    }
}
</script>