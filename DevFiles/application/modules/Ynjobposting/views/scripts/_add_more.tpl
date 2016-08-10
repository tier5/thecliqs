<?php
	$editorOptions['plugins'] = array(
        'table', 'fullscreen', 'media', 'preview', 'paste',
        'code', 'image', 'textcolor',  'link'
      );

	  $editorOptions['toolbar1'] = array(
	    'undo', 'redo', 'removeformat', 'pastetext', '|', 'code',
	    'media', 'image',  'link', 'fullscreen',
	    'preview'
	  );
?>
<div id="header_1-wrapper" class="form-wrapper">
	<div id="header_1-label" class="form-label">
		<?php echo $this->translate('Additional Information');?>
	</div>
	<div id="header_1-element" class="form-element">
		<p class='description'><?php echo $this->translate('Header');?></p>
		<input type="text" name="header_1"> 
		<div id='add_more_info'>Add more Info</div>
	</div>
</div>
<div id="content_1-wrapper" class="form-wrapper">
	<div id="content_1-label" class="form-label">
		&nbsp;
	</div>
	<div id="content_1-element" class="form-element">
		<p class='description'><?php echo $this->translate('Content');?></p>
		<?php echo $this -> formTinyMce('content_1', null, $editorOptions); ?>
	</div>
</div>	
