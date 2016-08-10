<?php if(count($this->tags) > 0):?>
<ul class = "global_form_box"  style="margin-bottom: 15px;">
    <span id="script" style="font-size: 9pt;">
         <?php
            $index = 0; $flag = false;
            foreach($this->tags as $tag):
                $index ++;
                if(trim($tag->text) != ""):
                if($index > $this -> numberOfTags && $flag == false): $flag = true;?>
                    <p id="showlink" style="display: block; font-weight: bold">[<a id = 'title' href="#" onclick="showhide('hide'); return(false);"><?php echo $this->translate('show all');?></a>]</p>
                    </span><span id="hide" style="display:none;font-size: 8pt;">
                <?php endif;?>
                <span style="<?php if($tag->count > 99 && $tag->count < 599): echo "font-size:".($tag->count/80 + 8)."pt"; elseif($tag->count > 599): echo "font-size: 14pt"; endif; ?>">
                <a  href='javascript:void(0);'onclick='javascript:tagAction(<?php echo $tag->tag_id; ?>);' ><?php echo $tag->text?></a> (<?php echo $tag->count?>)
            </span>
         <?php endif; endforeach;
         if($flag == true):?>
            <p id="hidelink" class="link_view" style="display: none;font-weight: bold">[<a id = 'title' href="#" onclick="showhide('hide'); return(false);"><?php echo $this->translate('hide');?></a>]</p>
         <?php endif; ?>
    </span>
 </ul>
<?php endif;?>

<script type="text/javascript">
  var tagAction = function(tag){
    window.location = "<?php echo $this -> url(array('action' => 'browse-videos'), 'ynvideochannel_general', true);?>" + "?tag=" + tag;
  };
  function showhide(id)
  {
    if (document.getElementById)
    {
        obj = document.getElementById(id);
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
</script>