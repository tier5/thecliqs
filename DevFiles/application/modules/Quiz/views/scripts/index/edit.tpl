<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Quiz
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: edit.tpl 2010-07-02 18:49 ermek $
 * @author     Ermek
 */
?>

<?php
  $this->headScript()
    ->appendFile($this->baseUrl().'/externals/autocompleter/Observer.js')
    ->appendFile($this->baseUrl().'/externals/autocompleter/Autocompleter.js')
    ->appendFile($this->baseUrl().'/externals/autocompleter/Autocompleter.Local.js')
    ->appendFile($this->baseUrl().'/externals/autocompleter/Autocompleter.Request.js')
    ->appendFile('application/modules/Quiz/externals/scripts/Quiz.js');
?>

<script type="text/javascript">
  en4.core.runonce.add(function()
  {
    new Autocompleter.Request.JSON('tags', '<?php echo $this->url(array('controller' => 'tag', 'action' => 'suggest'), 'default', true) ?>', {
      'postVar' : 'text',

      'minLength': 1,
      'selectMode': 'pick',
      'autocompleteType': 'tag',
      'className': 'tag-autosuggest',
      'filterSubset' : true,
      'multiple' : true,
      'injectChoice': function(token){
        var choice = new Element('li', {'class': 'autocompleter-choices', 'value':token.label, 'id':token.id});
        new Element('div', {'html': this.markQueryValue(token.label),'class': 'autocompleter-choice'}).inject(choice);
        choice.inputValue = token;
        this.addChoiceEvents(choice).inject(this.choices);
        choice.store('autocompleteChoice', token);
      }
    });
  });

  en4.core.runonce.add(function(){
    quiz.manage_navigation(<?php echo $this->step_info?>);
  });
</script>

<div class="headline">
  <h2>
    <?php echo $this->translate('Edit Quiz');?>
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
  <?php echo $this->form->render($this) ?>

  <br/>
  
  <div class="create_quiz_next">
    <button type="button" id="quiz_next_btn"><?php echo $this->translate('quiz_NEXT STEP >>>'); ?></button>
  </div>
  <br/>
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
