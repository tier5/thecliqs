<div class='global_form'>
<?php if($this->upload_message == 0): ?>
	<form action="" class="global_form_box" method="POST" id="upload_form" style="max-width: 495px">
	<div style="color: red"><?php echo $this->translate('Please read the terms of use and agree to check the box below.'); ?></div>
	<div style="margin-bottom: 5px; margin-top: 5px">
		<div style="padding: 5px; border: 1px solid #ccc; max-width: 480px" id = 'terms_conditions'>
		<?php  $terms = Engine_Api::_()->getApi('settings', 'core')->getSetting('mp3music.terms', "");   
		        if($terms != ""):
		            echo $terms;
		        else:
		            echo $this->translate('Only post songs in which they have the right to upload, etc.');
		         endif;     ?>
		</div>
	</div>
	         <label>
	         	<input type="checkbox" id="upload_message" name="upload_message" style="margin: 0px 7px 0 0">
	         	<?php echo $this->translate('I have read and fully agree with the terms.'); ?>
	         </label>
	         <br/>
	         <button type="submit" name="submit"> <?php echo $this->translate('Continue'); ?></button>
	</form>         
<?php else: ?>
<?php    
	$user = Engine_Api::_()->user()->getViewer();
         $max_albums =  Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('mp3music_album', $user, 'max_albums');
         if($max_albums == "")
         {
            $mtable  = Engine_Api::_()->getDbtable('permissions', 'authorization');
             $maselect = $mtable->select()
                ->where("type = 'mp3music_album'")
                ->where("level_id = ?",$user->level_id)
                ->where("name = 'max_albums'");
              $mallow_a = $mtable->fetchRow($maselect);          
              if (!empty($mallow_a))
                $max_albums = $mallow_a['value'];
              else
                 $max_albums = 10;
         }
         $cout_album = Mp3music_Model_Album::getCountAlbums($user);
        if($cout_album < $max_albums):
             echo $this->form->render($this);
        else: ?>
           <div style="color: red; padding-left: 300px;">
                <?php echo $this->translate("Sorry! Maximum number of allowed album : "); echo $max_albums; echo " albums" ; ?> 
           </div> 
        <?php endif; ?>
<?php endif; ?>

</div>
<script type="text/javascript">
function updateTextFields() {
  if ($('music_singer_id').selectedIndex > 0) {
    $('other_singer-wrapper').hide();
  } else {
    $('other_singer-wrapper').show();
  }
}
</script>