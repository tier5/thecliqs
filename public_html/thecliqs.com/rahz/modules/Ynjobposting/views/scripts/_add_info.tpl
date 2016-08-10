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
<?php if(!empty($this -> info)) :?>
<div class="form-elements">
<?php endif;?>
	<div id="header_1-wrapper" class="form-wrapper">
		<div id="header_1-label" class="form-label">
			<?php echo $this->translate('Header');?>
		</div>
		<div id="header_1-element" class="form-element">
			<input type="text" name="header_1" value="<?php if(!empty($this -> info)) echo  $this -> info -> header; else echo '';?>"> 
			<a href="javascript:void(0);" onclick="javascript:void(0)" class='fa fa-plus-circle' id='add_more_info'></a>
		</div>
	</div>
	<div id="content_1-wrapper" class="form-wrapper">
		<div id="content_1-label" class="form-label">
			<?php echo $this->translate('Content');?>
		</div>
		<div id="content_1-element" class="form-element">
			<?php 
				$value = null;
				if(!empty($this -> info))
				{
					$value = $this -> info -> content;
				}
			?>
			<?php echo $this -> formTinyMce('content_1', $value, $editorOptions); ?>
			<script type="text/javascript">
				tinymce.init({ mode: "exact", elements: "content_1", plugins: "table,fullscreen,media,preview,paste,code,image,textcolor", theme: "modern", menubar: false, statusbar: false, toolbar1: "undo,|,redo,|,removeformat,|,pastetext,|,code,|,media,|,image,|,link,|,fullscreen,|,preview", toolbar2: "fontselect,fontsizeselect,bold,italic,underline,strikethrough,forecolor,backcolor,|,alignleft,aligncenter,alignright,alignjustify,|,bullist,numlist,|,outdent,indent,blockquote", toolbar3: "", element_format: "html", height: "225px", convert_urls: false, language: "en", directionality: "ltr" });
			</script>
		</div>
	</div>	
<?php if(!empty($this -> info)) :?>
</div>
<?php endif;?>
