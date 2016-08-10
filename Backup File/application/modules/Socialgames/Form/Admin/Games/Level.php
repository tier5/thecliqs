<?php

class Socialgames_Form_Admin_Games_Level extends Authorization_Form_Admin_Level_Abstract
{
    public function init()
    {
        parent::init();

        $this->setTitle('Member Level Settings')
            ->setDescription('These settings are applied on a per member level basis. Start by selecting the member level you want to modify, then adjust the settings for that level below.');

        $this->addElement('Radio', 'view', array(
            'label' => 'Allow Viewing of Games?',
            'description' => 'Do you want to let members view games? If set to no, some other settings on this page may not apply.',
            'multiOptions' => array(
                1 => 'Yes, allow viewing of games.',
                0 => 'No, do not allow games to be viewed.',
            ),
            'value' => 1,
        ));
        if(!$this->isModerator()) {
            unset($this->view->options[2]);
        }

        if(!$this->isPublic()) {

           
            
            $this->addDisplayGroup(array('view'), 'settings', array(
                'class' => 'setting_fieldset', 
                'legend' => 'Settings'
                )
        	);

            $availableLabels = array(
                'everyone' => 'Everyone',
                'registered' => 'All Registered Members',
                'owner_network' => 'Friends and Networks',
                'owner_member_member' => 'Friends of Friends',
                'owner_member' => 'Friends Only',
                'owner' => 'Just Me',
            );
        }
    }
}
