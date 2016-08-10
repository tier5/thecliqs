<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: widget.tpl 2010-08-31 17:53 idris $
 * @author     Idris
 */
?>

<div style="padding: 10px;">

  <?php if( $this->form ): ?>

  <script type="text/javascript">
    window.addEvent('domready', function() {
      var params = parent.pullWidgetParams();
      var info = parent.pullWidgetTypeInfo();
      $H(params).each(function(value, key) {
        if( $(key) ) {
          $(key).value = value;
        }
      });
      $$('.form-description').set('html', info.description);
    })
  </script>

  <?php echo $this->form->render($this) ?>

  <?php elseif( $this->values ): ?>

  <script type="text/javascript">
    parent.setWidgetParams(<?php echo Zend_Json::encode($this->values) ?>);
    parent.Smoothbox.close();
  </script>

  <?php else: ?>

  <?php echo $this->translate("Error: no values") ?>

  <?php endif; ?>

</div>