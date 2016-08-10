<?php
	$allowed_html = 'strong, b, em, i, u, strike, sub, sup, p, div, pre, address, h1, h2, h3, h4, h5, h6, span, ol, li, ul, a, img, embed, br, hr, object , param, iframe';
	
	$editorOptions = array(
      'html' => (bool) $allowed_html,
    );
	$editorOptions['plugins'] =  array(
		   		'table', 'fullscreen', 'media', 'preview', 'paste',
		   		'code', 'image', 'textcolor'
    );
    $editorOptions['toolbar1'] = array(
	      'undo', '|', 'redo', '|', 'removeformat', '|', 'pastetext', '|', 'code', '|', 'media', '|', 
	      'image', '|', 'link', '|', 'fullscreen', '|', 'preview'
    );       
    $editorOptions['html'] = 1;
    $editorOptions['bbcode'] = 1;
	$editorOptions['mode'] = 'exact';
?>
<?php
	$labelHeader = $this -> labelHeader;
	$labelContent = $this -> labelContent;
?>

<?php if(!empty($this -> info)) :?>
<div class="form-elements">
<?php endif;?>
	<div id="<?php echo $labelHeader ?>-wrapper" class="form-wrapper">
		<div id="<?php echo $labelHeader ?>-label" class="form-label">
			<?php echo $this->translate('Header');?>
		</div>
		<div id="<?php echo $labelHeader ?>-element" class="form-element">
			<input type="text" id="<?php echo $labelHeader ?>" name="<?php echo $labelHeader ?>" value="<?php if(!empty($this -> info)) echo  $this -> info -> header; else echo '';?>"> 
			<a href="javascript:void(0);" onclick="javascript:void(0)" class='fa fa-minus-circle remove_content'></a>
		</div>
	</div>
	<div id="<?php echo $labelContent?>-wrapper" class="form-wrapper">
		<div id="<?php echo $labelContent?>-label" class="form-label">
			<?php echo $this->translate('Content');?>
		</div>
		<div id="<?php echo $labelContent?>-element" class="form-element">
			<?php 
				$value = null;
				if(!empty($this -> info))
				{
					$value = $this -> info -> content;
				}
			?>
			<?php echo $this -> formTinyMce($labelContent, $value, $editorOptions); ?>
			<script type="text/javascript">
				tinymce.init({ mode: "exact", elements: "<?php echo $labelContent;?>", plugins: "table,fullscreen,media,preview,paste,code,image,textcolor", theme: "modern", menubar: false, statusbar: false, toolbar1: "undo,|,redo,|,removeformat,|,pastetext,|,code,|,media,|,image,|,link,|,fullscreen,|,preview", toolbar2: "fontselect,fontsizeselect,bold,italic,underline,strikethrough,forecolor,backcolor,|,alignleft,aligncenter,alignright,alignjustify,|,bullist,numlist,|,outdent,indent,blockquote", toolbar3: "", element_format: "html", height: "225px", convert_urls: false, language: "en", directionality: "ltr" });
				  	$$('.remove_content').addEvent('click', function(){
						var removeParent = this.getParent().getParent().getParent();
						$('number_add_more').set('value', $('number_add_more').value - 1);
						removeParent.destroy();
					});
			</script>
		</div>
	</div>
<?php if(!empty($this -> info)) :?>
</div>
<?php endif;?>
