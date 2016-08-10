<?php

class Ynfbpp_View_Helper_FieldValueLooping extends Fields_View_Helper_FieldAbstract
{
    public function fieldValueLooping($subject, $partialStructure)
    {
        if (empty($partialStructure))
        {
            return '';
        }

        if (!($subject instanceof Core_Model_Item_Abstract) || !$subject -> getIdentity())
        {
            return '';
        }

        // Generate
        $content =  '';
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $show_hidden = $viewer -> getIdentity() ? ($subject -> getOwner() -> isSelf($viewer) || 'admin' === Engine_Api::_() -> getItem('authorization_level', $viewer -> level_id) -> type) : false;

        foreach ($partialStructure as $map)
        {

            // Get field meta object
            $field = $map -> getChild();
            $value = $field -> getValue($subject);

            if (!$field -> display && !$show_hidden)
            {

                continue;
            }
            else
            {
                // Normal fields
                $tmp = $this -> getFieldValueString($field, $value, $subject, $map, $partialStructure);
                if (!empty($tmp))
                {
                    $label = $this -> view -> translate($field -> label);
                    $content .= sprintf('<li><span>%s:</span> <span>%s</span></li>', $label, $tmp);
                }
            }
            
        }

        if(empty($content)){
            return '';
        }

        return '<ul class="uiYnfbppHovercardInfo">'. $content . '</ul>';
    }

    public function getFieldValueString($field, $value, $subject, $map = null, $partialStructure = null)
    {
        if ((!is_object($value) || !isset($value -> value)) && !is_array($value))
        {
            return null;
        }

        $helperName = Engine_Api::_() -> fields() -> getFieldInfo($field -> type, 'helper');
        if (!$helperName)
        {
            return null;
        }

        $helper = $this -> view -> getHelper($helperName);
        if (!$helper)
        {
            return null;
        }

        $helper -> structure = $partialStructure;
        $helper -> map = $map;
        $helper -> field = $field;
        $helper -> subject = $subject;
        $tmp = $helper -> $helperName($subject, $field, $value);
        unset($helper -> structure);
        unset($helper -> map);
        unset($helper -> field);
        unset($helper -> subject);

        return $tmp;
    }

}
