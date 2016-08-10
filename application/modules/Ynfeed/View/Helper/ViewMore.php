<?php
class Ynfeed_View_Helper_ViewMore extends Engine_View_Helper_ViewMore
{
	public function viewMore($string, $moreLength = null, $maxLength = null, 
      $lessLength = null, $nl2br = true)
   {
    if( !is_numeric($moreLength) || $moreLength <= 0 ) {
      $moreLength = $this->_moreLength;
    }
    if( !is_numeric($maxLength) || $maxLength <= 0 ) {
      $maxLength = $this->_maxLength;
    }
    if( !is_numeric($lessLength) || $lessLength <= 0 ) {
      $lessLength = $this->_lessLength;
    }

    // If using line breaks, ensure that there are not too many line breaks
    if( $nl2br ) {
      $string = trim(preg_replace('/[\r\n]+/', "\n", $string));
      if( ($c = substr_count($string, "\n")) > $this->_maxLineBreaks) {
        $pos = 0;
        for( $i = 0; $i < $this->_maxLineBreaks; $i++ ) {
          $pos = strpos($string, "\n", $pos + 1);
        }
        if( $pos <= 0 || !is_int($pos) ) {
          $pos = null;
        }
        if( $pos && $pos < $moreLength ) {
          $moreLength = $pos;
        }
      }
    }
    
    // If length is less than max len, just return
    $strLen = Engine_String::strlen(strip_tags(preg_replace('#<a.*?>([^>]*)</a>#i', '$1', $string)));
    if( $strLen <= $moreLength + $this->_fudgesicles ) {
      if( $nl2br ) {
        return nl2br($string);
      } else {
        return $string;
      }
    }
    
    // Otherwise truncate
    if( $strLen >= $maxLength ) {
      $strLen = $maxLength;
      $string = $this -> html_substr($string, 0, $maxLength, true) . $this->view->translate('... &nbsp;');
    }
    
    $shortText = $this -> html_substr($string, 0, $moreLength, true);
    $fullText = $string;
    
    // Do nl2br
    if( $nl2br ) {
      $shortText = nl2br($shortText);
      $fullText = nl2br($fullText);
    }
    
    $onclick = <<<EOF
var me = $(this).getParent(), other = $(this).getParent().getNext(), fn = function() {
  me.style.display = 'none';
  other.style.display = '';
};
fn();
setTimeout(fn, 0);
EOF;
    $content = '<'
      . $this->_tag
      . ' class="view_more"'
      . '>'
      . $shortText
      . $this->view->translate('... &nbsp;')
      . '<a class="view_more_link" href="javascript:void(0);" onclick="' . htmlspecialchars($onclick) . '">'
      . $this->view->translate('more')
      . '</a>'
      . '</'
      . $this->_tag
      . '>'
      . '<'
      . $this->_tag
      . ' class="view_more"'
      . ' style="display:none;"'
      . '>'
      . $fullText
      . ' &nbsp;'
      ;

    if( $strLen >= $lessLength ) {
      $onclick = <<<EOF
var me = $(this).getParent(), other = $(this).getParent().getPrevious(), fn = function() {
  me.style.display = 'none';
  other.style.display = '';
};
fn();
setTimeout(fn, 0);
EOF;
      $content .= '<a class="view_less_link" href="javascript:void(0);" onclick="' . htmlspecialchars($onclick) . '">'
          . $this->view->translate('less')
          . '</a>';
    }

    $content .= '</'
      . $this->_tag
      . '>'
      ;

    return $content;
  }
  function html_substr($s, $srt, $len = NULL, $strict = false, $suffix = NULL) {
		if (is_null($len)) 
		{
			 $len = strlen($s);
		}

		$f = 'static $strlen=0; 
			if ( $strlen >= ' . $len . ' ) { return "><"; } 
			$html_str = html_entity_decode( $a[1] );
			$subsrt   = max(0, (' . $srt . '-$strlen));
			$sublen = ' . (empty($strict) ? '(' . $len . '-$strlen)' : 'max(@strpos( $html_str, "' . ($strict === 2 ? '.' : ' ') . '", (' . $len . ' - $strlen + $subsrt - 1 )), ' . $len . ' - $strlen)') . ';
			$new_str = substr( $html_str, $subsrt,$sublen); 
			$strlen += $new_str_len = strlen( $new_str );
			$suffix = ' . (!empty($suffix) ? '($new_str_len===$sublen?"' . $suffix . '":"")' : '""') . ';
			return ">" . htmlentities($new_str, ENT_QUOTES, "UTF-8") . "$suffix<";';

		return preg_replace(array("#<[^/][^>]+>(?R)*</[^>]+>#", "#(<(b|h)r\s?/?>){2,}$#is"), "", trim(rtrim(ltrim(preg_replace_callback("#>([^<]+)<#", create_function('$a', $f), ">$s<"), ">"), "<")));
	}
}
