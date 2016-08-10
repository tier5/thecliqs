<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideo
 * @author     YouNet Company
 */
?>
<?php 
if($this->contest_type == 'advalbum' || $this->contest_type == 'ynvideo')
{
	$item = $this->item;
	$owner = $this->item;
	$description = $this->translate(array('%1$s view', '%1$s views', $item->view_count), $this->locale()->toNumber($item->view_count)); 
}
elseif($this->contest_type == 'mp3music' )
{
	$item = $this->item;
	$owner = $this->album;
	$description = $this->translate(array('%1$s play', '%1$s plays', $item->play_count), $this->locale()->toNumber($item->play_count));
}
elseif($this->contest_type == 'ynmusic' )
{
	$item = $this->item;
	$owner = $this->item;
	$description = $this->translate(array('%1$s play', '%1$s plays', $item->play_count), $this->locale()->toNumber($item->play_count));
}
else{
	$item = $this->item;
	$owner = $this->item;
	$description = $this->translate(array('%1$s view', '%1$s views', $item->view_count), $this->locale()->toNumber($item->view_count));
}
?>
<div class="ynContestSubmit_thumb_wrapper ynContest_thumb_wrapper">
  <?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($owner, 'thumb.profile'), array('class' => 'thumb')) ?>  
</div>
<?php 
    echo $this->htmlLink($item->getHref(), 
            $this->string()->truncate($item->getTitle(), 30), 
            array('class' => 'ynContestSubmit_title', 'title' => $item->getTitle())) 
?>

<div class="ynContest_author">	
    <span class="ynContest_views">        
		
		<?php echo $description;?>        
    </span>
    <div class="video_option_<?php echo $item->getIdentity()?>">
 		<input id="<?php echo $this->contest_type?>_<?php echo $item->getIdentity()?>" type="radio" 
 		<?php if(isset($_SESSION[$this->contest_type]) && $_SESSION[$this->contest_type]== $item->getIdentity()):?>
 			checked
 		<?php else:?>
 		
 		<?php endif;?>
 		  value="<?php echo $item->getIdentity()?>" for="<?php echo $this->contest_type?>" onclick="getOption('<?php echo $this->contest_type?>');" name="<?php echo $this->contest_type?>">
 		  <label for="<?php echo $this->contest_type?><?php echo $item->getIdentity()?>" class="optional"><?php echo $this->translate("Choose")?></label>
    </div>
</div>

   
<script type="text/javascript">
function getOption (optionType){

	inputs = document.getElementsByName(optionType); 
	for (var i = 0; i < inputs.length; i++) {
         if (inputs[i].checked) {
        	 id = inputs[i].value;
         }
    }
   
	en4.core.request.send(new Request.HTML({
 	url : en4.core.baseUrl + 'contest/my-entries/get-value/',
     data : {
     	format : 'html',
     	id :id, 
     	optionType :  optionType, 
     	music_type : '<?php echo $this->music_type;?>'              
 	},
 	 onComplete : function(response)
      {
 		$(form_item_import).setStyle('display','block');
      }
 	}));     
   
    
	
}
 </script>