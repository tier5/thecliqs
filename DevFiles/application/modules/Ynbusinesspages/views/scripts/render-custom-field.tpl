<?php $customFields = $this->category->getCustomFieldsList();?>

<?php foreach ($customFields as $field) : ?>
<li class="business-customField_<?php echo $field->field_id?>">
    <?php
    $tmp = '';
    $partialStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($this->business);
    foreach( $partialStructure as $map ) {
        // Get field meta object
        $field_struct = $map->getChild();
        if ($field_struct -> field_id == $field->field_id) {
            $value = $field_struct->getValue($this->business);
            if (!$field_struct || $field_struct->type == 'profile_type') continue;
            if (!$field_struct->display) continue;
            // Normal fields
            $helperName = Engine_Api::_()->fields()->getFieldInfo($field->type, 'helper');
            if($helperName)
            {
                $helper = $this->getHelper($helperName);
                if($helper)
                {
                    $helper->structure = $partialStructure;
                    $helper->map = $map;
                    $helper->field = $field;
                    $helper->subject = $subject;
                    $tmp = $helper->$helperName($this->business, $field_struct, $value);
                    unset($helper->structure);
                    unset($helper->map);
                    unset($helper->field);
                    unset($helper->subject);
                }
            }
        }
    }
    echo $tmp;?>
</li>
<?php endforeach;?>