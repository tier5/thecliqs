<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideochannel
 * @author     YouNet Company
 */

class Ynvideochannel_Form_Channel_Edit extends Ynvideochannel_Form_Channel_Create
{
    protected $_channel;

    public function getChannel()
    {
        return $this->_channel;
    }

    public function setChannel($value)
    {
        $this->_channel = $value;
    }
    public function init()
    {
        parent::init();
        $this -> removeElement('add_channel');
        $this -> removeElement('videos');
        $this -> initValueForElements();
        if($this->getChannel()->auto_update == 1) {
            $this->addElement('Checkbox', 'stop_auto_update', array(
                'label' => "Stop automatically get videos by time settings",
            ));
        }

        $this -> addElement('Dummy', 'videos', array('decorators' => array(
            array(
                'ViewScript',
                array(
                    'viewScript' => '_channel_edit_video_listing.tpl',
                    'class' => 'form element',
                    'channel' => $this -> getChannel(),
                    'noedit' => true
                )
            )),
        ));
    }

    protected function initValueForElements() {
        $this->populate($this->_channel->toArray());
        // set view authentication and comment authentication for the two dropdownlists
        $authViewElement = $this->getElement('auth_view');
        $authCommentElement = $this->getElement('auth_comment');

        $auth = Engine_Api::_()->authorization()->context;
        if ($authViewElement) {
            foreach ($this->_roles as $key => $role) {
                if ($auth->isAllowed($this->_channel, $key, 'view')) {
                    $authViewElement->setValue($key);
                    break;
                }
            }
        }

        if ($authCommentElement) {
            foreach ($this->_roles as $key => $role) {
                if ($auth->isAllowed($this->_channel, $key, 'comment')) {
                    $authCommentElement->setValue($key);
                    break;
                }
            }
        }
    }

}