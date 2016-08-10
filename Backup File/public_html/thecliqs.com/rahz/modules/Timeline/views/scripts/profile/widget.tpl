<?php
/**
 * SocialEngine
 *
 * @category Application_Extensions
 * @package Timeline
 * @copyright Copyright Hire-Experts LLC
 * @license http://www.hire-experts.com
 * @version $Id: widget.tpl 2/9/12 12:46 PM mt.uulu $
 * @author Mirlan
 */
?>

<div id="tl-content">
  <div class="tl-widget">

    <div class="w-header">
<!--      --><?php //echo $this->translate($this->widget->params['title']); ?>
    </div>

    <div class="w-content">
<!--      --><?php //echo $this->content()->renderWidget($this->widget->name); ?>
      <?php echo $this->content()->renderWidget($this->tabs->name, array('content_id'=>$this->tabs->content_id)); ?>
    </div>

  </div>
</div>