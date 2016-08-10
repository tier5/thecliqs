<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ravshanbek
 * Date: 28.08.12
 * Time: 16:56
 * To change this template use File | Settings | File Templates.
 */
?>

<script type="text/javascript">
   en4.core.runonce.add(function(){
     var settings = {
       'like_button' : <?php echo (int)@$this->like_button ?>,
       'donate_button': <?php echo (int)@$this->donate_button ?>,
       'show_supporters': <?php echo (int)@$this->show_supporters ?>
   };
     var $like_button = $('like_button');
     var $donate_button = $('donate_button');
     var $show_supporters = $('show_supporters');

     var $donate_box_frame = $('donate_box_frame');
     var src = "<?php echo $this->base_url.$this->url(array(
         'controller' => 'donation',
         'action' => 'show-donate-box',
         'object' => 'donation',
         'object_id' => $this->donation->getIdentity()
       ),
       'donation_extended', true
     );
     ?>";
     var updateSettings = function(){

       var iframeSrc = src;
       for(var setting in settings){
         iframeSrc = iframeSrc + '/' + setting + '/' + settings[setting];
       }
       $donate_box_frame.src = iframeSrc;
       $('donate_box_snippet').value = $('iframeContainer').innerHTML;
     }
     $like_button.addEvent('click',function(){
       if($like_button.checked){
         settings['like_button'] = 1;
       } else {
         settings['like_button'] = 0;
       }
       updateSettings();
     });

     $donate_button.addEvent('click',function(){
       if($donate_button.checked){
         settings['donate_button'] = 1;
       }
       else{
         settings['donate_button'] = 0;
       }
       updateSettings();
     });

     $show_supporters.addEvent('click',function(){
       if($show_supporters.checked){
         settings['show_supporters'] = 1;
       }
       else{
         settings['show_supporters'] = 0;
       }
       updateSettings();
     });
   });

</script>

<div class="donation_promote_box">
    <h2>
      <?php echo $this->translate("DONATION_PROMOTE_BOX_TITLE"); ?>
    </h2>
    <br/>

    <div class="promote_left">
        <div class="donate_desc">
          <?php echo $this->translate('DONATION_Donate_Box_Code:'); ?>
        </div>
        <div class="donate_textarea">
            <textarea rows="7" cols="10" onfocus="this.select();" name="donate_box_snippet" id="donate_box_snippet"><iframe scrolling="no" frameborder="0" style="background: transparent; border: none; overflow: hidden; width: 255px; height: 310px;" allowTransparency="true" src="<?php echo 'http://'.$_SERVER['HTTP_HOST'].$this->url(array('controller' => 'donation', 'action' => 'show-donate-box', 'object' => 'donation', 'object_id' => $this->donation->getIdentity(), 'like_button' => $this->like_button, 'donate_button' => $this->donate_button, 'show_supporters' => $this->show_supporters), 'donation_extended', true); ?>"></iframe></textarea>
        </div>
        <br/>
        <div class="donate_desc">
            <?php echo $this->translate('DONATION_Options to show:'); ?>
        </div>
        <div class="show_options">
            <input id="like_button" type="checkbox" checked="checked">
            <label for="like_button"><?php echo $this->translate('DONATION_Like Button'); ?></label>
            <input id="donate_button" type="checkbox" checked="checked">
            <label for="donate_button"><?php echo $this->translate('DONATION_Donate Button'); ?></label>
            <input id="show_supporters" type="checkbox" checked="checked">
            <label for="show_supporters"><?php echo $this->translate('DONATION_Supporters'); ?></label>
        </div>
    </div>

    <div class="promote_right">
      <div class="donate_desc">
          <?php echo $this->translate('DONATION_Donate_Box_Preview:'); ?>
      </div>
      <div id="iframeContainer"><iframe scrolling="no" frameborder="0" style="background: transparent; border: none; overflow: hidden; width: 255px; height: 310px;" allowTransparency="true" src="<?php echo 'http://'.$_SERVER['HTTP_HOST'].$this->url(array('controller' => 'donation', 'action' => 'show-donate-box', 'object' => 'donation', 'object_id' => $this->donation->getIdentity(), 'like_button' => $this->like_button, 'donate_button' => $this->donate_button, 'show_supporters' => $this->show_supporters), 'donation_extended', true); ?>" id="donate_box_frame"></iframe></div>
    </div>
</div>