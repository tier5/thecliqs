window.addEvent('load', function(){
  HESuggest.init('<?php echo $this->url(array(
    "controller" => "index",
    "action" => "suggest",
    "object_id" => $this->options["params"]["object_id"],
    "object_type" => $this->options["params"]["object_type"],
    "suggest_type" => $this->options["params"]["suggest_type"]
  ), "suggest_general"); ?>', <?php echo Zend_Json_Encoder::encode($this->options); ?>);
  window.setTimeout(function(){
    HESuggest.open();
  }, <?php echo (int)$this->options['timeout']; ?>);
});