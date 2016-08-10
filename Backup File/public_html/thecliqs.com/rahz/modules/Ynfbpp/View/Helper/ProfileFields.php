<?php

class Ynfbpp_View_Helper_ProfileFields extends Zend_View_Helper_Abstract
{

    public function profileFields($subject, $viewer = null)
    {

        if ($viewer == NULL)
        {
            $viewer = Engine_Api::_() -> user() -> getViewer();
        }
        // does not allow to view field.
        if (!$subject -> authorization() -> isAllowed($viewer, 'view'))
        {
            return '';
        }

        // Load fields view helpers
        $view = $this -> view;

        // Values
        $fieldStructure = Engine_Api::_() -> fields() -> getFieldsStructurePartial($subject);

        if (count($fieldStructure) <= 1)
        {
            // @todo figure out right logic
            return '';
        }

        $orderings = Engine_Api::_() -> ynfbpp() -> getUserPopupProfileFields();

        foreach ($fieldStructure as $key => $map)
        {
            $childId = $map -> child_id;
            if (isset($orderings[$childId]))
            {
                $orderings[$childId] = $map;
            }
        }

        foreach ($orderings as $key => $value)
        {
            if (is_int($value))
            {
                unset($orderings[$key]);
            }
        }
        if (empty($orderings))
        {
            return '';
        }
        $content = '';
        $limit = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('ynfbpp.user.fieldlimit', 3);
        $limit = (int)$limit;

        foreach ($orderings as $key => $map)
        {
            if ($map instanceof Fields_Model_Map)
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
                    $tmp = $this -> getFieldValueString($field, $value, $subject, $map, $fieldStructure);
                    if (!empty($tmp))
                    {
						$tmp = $this -> view -> string() -> truncate($tmp, 140);
                        $label = $this -> view -> translate($field -> label);
                        $content .= sprintf('<li class="uiYnfbppRow"><div>%s: </div><span>%s</span></li>', $label, $tmp);
                        if (--$limit <= 0)
                        {
                            break;
                        }
                    }
                }
            }
        }
        return $content;
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

        return trim($tmp);
    }

}
