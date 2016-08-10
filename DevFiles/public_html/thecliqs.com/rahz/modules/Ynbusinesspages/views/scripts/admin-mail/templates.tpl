<h2><?php echo $this->translate("YouNet Business Pages Plugin") ?></h2>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>
<div class='clear'>
  <div class='settings'>
  <?php echo $this->form->render($this); ?>
	</div>
	</div>



<script type="text/javascript">
  var mailTemplateLanguage = '<?php echo $this->language ?>';
  
  var setEmailLanguage = function(language) {
    var url = '<?php echo $this->url(array('language' => null, 'template' => null)) ?>';
    window.location.href = url + '/language/' + language;
  }

  var fetchEmailTemplate = function(template_id) {
    var url = '<?php echo $this->url(array('language' => null, 'template' => null)) ?>';
    window.location.href = url + '/language/' + mailTemplateLanguage + '/template/' + template_id;
  }

</script>
<style type="text/css">
.tabs > ul > li {
    display: block;
    float: left;
    margin: 2px;
    padding: 5px;
}
.tabs > ul {  
 display: table;
  height: 65px;
}
</style>   