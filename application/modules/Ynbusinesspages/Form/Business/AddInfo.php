<?php
class Ynbusinesspages_Form_Business_AddInfo extends Engine_Form
{
  protected $_labelHeader;
  protected $_labelContent;
	
	public function getLabelContent()
	{
		return $this -> _labelContent;
	}
	
	public function setLabelContent($labelContent)
	{
		$this -> _labelContent = $labelContent;
	} 
	
	public function getLabelHeader()
	{
		return $this -> _labelHeader;
	}
	
	public function setLabelHeader($labelHeader)
	{
		$this -> _labelHeader = $labelHeader;
	} 
  	
  public function init()
  {
	 
	$allowed_html = 'strong, b, em, i, u, strike, sub, sup, p, div, pre, address, h1, h2, h3, h4, h5, h6, span, ol, li, ul, a, img, embed, br, hr, object , param, iframe';
	
	$editorOptions = array(
      'html' => (bool) $allowed_html,
    );
	
	$editorOptions['plugins'] = array(
        'table', 'fullscreen', 'media', 'preview', 'paste',
        'code', 'image', 'textcolor', 'link'
      );

	  $editorOptions['toolbar1'] = array(
	    'undo', 'redo', 'removeformat', 'pastetext', '|', 'code',
	    'media', 'image', 'link', 'fullscreen',
	    'preview'
	  );
	  
	 $this -> addElement('Dummy', 'addmore', array(
		'decorators' => array( array(
			'ViewScript',
			array(
				'viewScript' => '_add_more_info.tpl',
				'class' => 'form element',
				'labelContent' => $this -> _labelContent,
				'labelHeader' => $this -> _labelHeader,
			)
		)), 
	));	
  }
}
