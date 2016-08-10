<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Quiz
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2010-07-02 18:44 ermek $
 * @author     Ermek
 */
?>

<script type="text/javascript">
  var pageAction =function(page){
    $('page').value = page;
    $('filter_form').submit();
  }
</script>

<div class="headline">
  <h2>
    <?php echo $this->translate('Quizzes');?>
  </h2>
  <div class="tabs quizzes_tabs">
    <?php
      // Render the menu
      echo $this->navigation()
        ->menu()
        ->setContainer($this->navigation)
        ->render();
    ?>
  </div>
</div>

<br/>

<div class="quiz_theme_<?php echo $this->theme_name; ?>">
<div>

<div class='layout_left layout_middle' style="width: 75%">

  <?php if( $this->browse_paginator->getTotalItemCount() > 0 ): ?>
    <div class="quizzes_browse">
      <?php foreach( $this->browse_paginator as $quiz_item ): ?>
        <div class="quiz_item">
          <div class='quizzes_browse_photo float_right_rtl'>
            <?php echo $this->htmlLink($quiz_item->getHref(), $this->itemPhoto($quiz_item, 'thumb.normal')) ?>
          </div>
          <div class='quizzes_browse_info'>
            <p class='quizzes_browse_info_title'>
              <?php echo $this->htmlLink($quiz_item->getHref(), $quiz_item->getTitle()) ?>
            </p>
            <p class='quizzes_browse_info_date'>
              <?php echo $this->translate('quiz_Posted');?>
              <?php echo $this->timestamp(strtotime($quiz_item->creation_date)) ?>
              <?php echo $this->translate('quiz_by');?>
              <?php echo $this->htmlLink($quiz_item->getOwner()->getHref(), $quiz_item->getOwner()->getTitle()) ?>
              &nbsp;-&nbsp;
              <?php echo $this->translate(array('quiz_<b>%s</b> view', '<b>%s</b> views', $quiz_item->view_count), $this->locale()->toNumber($quiz_item->view_count)) ?>
              &nbsp;-&nbsp;
              <?php echo $this->translate(array('quiz_<b>%s</b> take', '<b>%s</b> takes', $quiz_item->take_count), $this->locale()->toNumber($quiz_item->take_count)) ?>
            </p>
            <div class="rate_quiz_item">
              <?php echo $this->itemRate('quiz', $quiz_item->getIdentity()); ?>
            </div>
            <p class='quizzes_browse_info_blurb'>
              <?php
                // Not mbstring compat
                echo $quiz_item->getDescription(true, 200);
              ?>
            </p>
          </div>
          <div class="clr"></div>
        </div>
      <?php endforeach; ?>
    </div>
  
  <?php elseif( $this->category || $this->show == 2 || $this->search ):?>
    <div class="tip">
      <span>
        <?php echo $this->translate('There are no quizzes with that criteria.');?>
        <?php if ($this->can_create): ?>
          <?php echo $this->translate('quiz_Be the first to %1$screate%2$s one!', '<a href="'.$this->url(array(), 'quiz_create').'">', '</a>'); ?>
        <?php endif; ?>
      </span>
    </div>

  <?php else:?>
    <div class="tip">
      <span>
        <?php echo $this->translate('There are no quizzes.'); ?>
        <?php if ($this->can_create):?>
          <?php echo $this->translate('quiz_Be the first to %1$screate%2$s one!', '<a href="'.$this->url(array(), 'quiz_create').'">', '</a>'); ?>
        <?php endif; ?>
      </span>
    </div>
  <?php endif; ?>

  <div class='browse_nextlast'>
    <?php echo $this->paginationControl($this->browse_paginator, null, array("pagination/quizpagination.tpl","quiz")); ?>
  </div>

</div>


<div class='layout_right'>
  <?php echo $this->form->render($this) ?>

  <?php if( $this->can_create): ?>
  <div class="quicklinks" style="margin-bottom:15px;">
    <ul>
      <li>
        <a href='<?php echo $this->url(array(), 'quiz_create', true) ?>' class='buttonlink icon_quiz_new'><?php echo $this->translate('Create New Quiz');?></a>
      </li>
    </ul>
  </div>
  <?php endif; ?>

  <?php echo $this->content()->renderWidget('quiz.most-taken'); ?>
  <?php echo $this->content()->renderWidget('quiz.recent-taken'); ?>
  <?php echo ($this->rateEnabled) ? $this->content()->renderWidget('rate.quiz-rate') : ''; ?>
  
</div>

</div>
</div>