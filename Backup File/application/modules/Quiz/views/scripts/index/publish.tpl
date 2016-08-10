<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Quiz
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: publish.tpl 2010-07-02 18:20 ermek $
 * @author     Ermek
 */
?>

<?php
  $this->headScript()
    ->appendFile('application/modules/Quiz/externals/scripts/Quiz.js')
?>


<script type="text/javascript">
en4.core.runonce.add(function(){
  quiz.manage_publish();
  quiz.manage_navigation(<?php echo $this->step_info?>);
});
</script>

<div class="headline">
  <h2>
    <?php echo $this->translate('Publish quiz'); ?>
  </h2>
  <div class="tabs">
    <?php
      // Render the menu
      echo $this->navigation()
        ->menu()
        ->setContainer($this->navigation)
        ->render();
    ?>
  </div>
</div>

<div class="quiz_edit_title">
  <span class="float_right_rtl"><?php echo $this->quiz->getTitle(); ?></span>&nbsp;&nbsp;&nbsp;
  <?php if ($this->quiz->published) : ?>
    <span style="color: green;" class="quizzes_pub_app"><?php echo $this->translate('quiz_published'); ?></span>&nbsp;&nbsp;&nbsp;
  <?php else: ?>
    <span style="color: red;" class="quizzes_pub_app"><?php echo $this->translate('quiz_not published'); ?></span>&nbsp;&nbsp;&nbsp;
  <?php endif; ?>
  <?php if ($this->quiz->approved) : ?>
    <span style="color: green;" class="quizzes_pub_app"><?php echo $this->translate('quiz_approved'); ?></span>&nbsp;&nbsp;&nbsp;
  <?php else : ?>
    <span style="color: red;" class="quizzes_pub_app"><?php echo $this->translate('quiz_not approved'); ?></span>&nbsp;&nbsp;&nbsp;
  <?php endif; ?>
</div>

<div class="layout_left" style="width: auto">
  <div class="quiz_publish_form">
    <?php echo $this->form->render($this) ?>
  </div>
</div>


<div class="layout_right">
<?php if( count($this->quiz_navigation) ): ?>
  <div class='headline tabs quiz_tabs'>
    <?php
      // Render the menu
      echo $this->navigation()->menu()->setContainer($this->quiz_navigation)->render()
    ?>
  </div>
<?php endif; ?>
</div>