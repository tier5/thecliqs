<?php echo $this->partial('_job_listing.tpl', 'ynjobposting', array(
		'paginator' => $this -> paginator,
		'jobIds' => $this -> jobIds,
		'idName' => 'hot-job',
		'idPrefix' => 'HotJob',
		'mode_enabled' => $this -> mode_enabled,
		'formValues' => $this->formValues,
		'widgetId' => $this->identity
));
?>