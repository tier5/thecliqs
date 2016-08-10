<?php echo $this->partial('_business_listing.tpl', 'ynbusinesspages', array(
    'paginator' => $this -> paginator,
    'businessIds' => $this -> businessIds,
    'idName' => 'business-listing',
    'idPrefix' => 'BusinessListing',
    'mode_enabled' => $this -> mode_enabled,
    'formValues' => $this->formValues,
    'class_mode' => $this->class_mode,
));
?>