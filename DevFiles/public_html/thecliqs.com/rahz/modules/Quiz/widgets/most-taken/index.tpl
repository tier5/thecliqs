<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Quiz
 * @copyright  Copyright 2006-2010 Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl idris $
 * @author     Idris
 */
?>

<h3><?php echo $this->translate('Most Taken Quizzes'); ?></h3>

<ul>
<?php foreach($this->quizes as $quiz): ?>
<li class="he_quiz_block">
  <?php echo $this->htmlLink($quiz->getHref(), $this->ItemPhoto($quiz, 'thumb.icon'), array('class' => 'widget_quiz_photo')); ?>
  <div class="he_quiz_info">
    <div class="he_quiz_title">
      <?php echo $this->htmlLink($quiz->getHref(), $quiz->getTitle()); ?>
    </div>
    <div class="he_quiz_desc"><?php echo $quiz->getDescription(true); ?></div>
    <div class="he_quiz_misc">
        <?php echo $this->translate(array('quiz_<b>%s</b> take', '<b>%s</b> takes', $quiz->take_count), '<span class="he_quiz_misc_important">' . $quiz->take_count . '</span>') ?>
    </div>
  </div>
</li>
<?php endforeach; ?>
</ul>