en4.core.runonce.add(function(){
  HESuggest.init('<?php echo $this->url(array(
    "controller" => "index",
    "action" => "suggest",
    "object_id" => $this->options["params"]["object_id"],
    "object_type" => $this->options["params"]["object_type"],
    "suggest_type" => $this->options["params"]["suggest_type"]
  ), "suggest_general"); ?>', <?php echo Zend_Json_Encoder::encode($this->options); ?>);
  HESuggest.initLink();
  $$('.suggest_link').addEvent('click', function(e){
    e.stop();
    HESuggest.open();
  });
});