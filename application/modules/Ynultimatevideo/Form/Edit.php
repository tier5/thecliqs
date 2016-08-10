<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynultimatevideo
 * @author     YouNet Company
 */
class Ynultimatevideo_Form_Edit extends Ynultimatevideo_Form_Video {
    protected $_video;

    public function getVideo()
    {
        return $this->_video;
    }

    public function setVideo($value)
    {
      $this->_video = $value;
    }

    protected function initValueForElements() {
        
        $this->populate($this->_video->toArray());
        
        // prepare tags
        $videoTags = $this->_video->tags()->getTagMaps();

        $tagString = '';
        foreach ($videoTags as $tagmap) {
            if ($tagString !== '')
                $tagString .= ', ';
            $tagString .= $tagmap->getTag()->getTitle();
        }

        $this->tags->setValue($tagString);
        
        // set view authentication and comment authentication for the two dropdownlists
        $authViewElement = $this->getElement('auth_view');
        $authCommentElement = $this->getElement('auth_comment');

        $auth = Engine_Api::_()->authorization()->context;
        if ($authViewElement) {
            foreach ($this->_roles as $key => $role) {
                if ($auth->isAllowed($this->_video, $key, 'view')) {
                    $authViewElement->setValue($key);
                    break;
                }
            }
        }

        if ($authCommentElement) {
            foreach ($this->_roles as $key => $role) {
                if ($auth->isAllowed($this->_video, $key, 'comment')) {
                    $authCommentElement->setValue($key);
                    break;
                }
            }
        }
    }
    
    protected function addAdditionalElements() 
    {
    	if(Engine_Api::_()->getApi('settings', 'core')->getSetting('ynultimatevideo_youtube_allow', 0))
		{
			$this -> addElement('Checkbox', 'allow_upload_channel', array(
				'label' => 'Also upload this video to YouTube',
			));
		}	
    }
}