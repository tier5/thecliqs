<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynprofilestyler
 * @author     YouNet Company
 */

class Ynprofilestyler_Form_Decorator_Div extends Zend_Form_Decorator_HtmlTag
{
	public function getElementAttribs()
	{
		if (null === ($element = $this->getElement()))
		{
			return null;
		}

		$attribs = $element->getAttribs();
		if (isset($attribs['helper']))
		{
			unset($attribs['helper']);
		}

		if (method_exists($element, 'getSeparator'))
		{
			if (null !== ($listsep = $element->getSeparator()))
			{
				$attribs['listsep'] = $listsep;
			}
		}

		if (isset($attribs['id']))
		{
			return $attribs;
		}

		$id = $element->getName();

		if ($element instanceof Zend_Form_Element)
		{
			if (null !== ($belongsTo = $element->getBelongsTo()))
			{
				$belongsTo = preg_replace('/\[([^\]]+)\]/', '-$1', $belongsTo);
				$id = $belongsTo . '-' . $id;
			}
		}

//		$element->setAttrib('id', $id);
//		$attribs['id'] = $id;

		return $attribs;
	}
	
	public function render($content)
	{
		$tag = $this->getTag();
		$placement = $this->getPlacement();
		$noAttribs = $this->getOption('noAttribs');
		$openOnly = $this->getOption('openOnly');
		$closeOnly = $this->getOption('closeOnly');
		$this->removeOption('noAttribs');
		$this->removeOption('openOnly');
		$this->removeOption('closeOnly');

		$attribs = null;
		if (!$noAttribs)
		{
			$attribs = $this->getElementAttribs();
		}

		switch ($placement)
		{
			case self::APPEND:
				if ($closeOnly)
				{
					return $content . $this->_getCloseTag($tag);
				}
				if ($openOnly)
				{
					return $content . $this->_getOpenTag($tag, $attribs);
				}
				return $content . $this->_getOpenTag($tag, $attribs) . $this->_getCloseTag($tag);
			case self::PREPEND:
				if ($closeOnly)
				{
					return $this->_getCloseTag($tag) . $content;
				}
				if ($openOnly)
				{
					return $this->_getOpenTag($tag, $attribs) . $content;
				}
				return $this->_getOpenTag($tag, $attribs) . $this->_getCloseTag($tag) . $content;
			default:
				return (($openOnly || !$closeOnly) ? $this->_getOpenTag($tag, $attribs) : '') . $content . (($closeOnly || !$openOnly) ? $this->_getCloseTag($tag) : '');
		}
	}
}
