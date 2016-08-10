<?php
if($this->canUpload):
 ?>
<h2><?php echo $this->translate('Auction Listing Photos');?></h2>
<?php echo $this->form->render($this) ?>
 <script type="text/javascript">
    //<!--
    en4.core.runonce.add(function() {
      var addMoreFile = window.addMoreFile = function () 
      {
        var fileElement = new Element('input', {
          'type': 'file',
          'name': 'photos[]',
          'multiple': "multiple"
        });
        fileElement.inject($('photos-element'));
      }
    });
    // -->
  </script>
<?php  else: ?>
<div class="tip" style="clear: inherit;">
      <span>
<?php  echo $this->translate('You can not upload photos!');?>
 </span>
           <div style="clear: both;"></div>
    </div>
<?php endif; ?>