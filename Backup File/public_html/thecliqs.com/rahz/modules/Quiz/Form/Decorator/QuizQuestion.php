<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Quiz
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: QuizQuestion.php 2010-07-02 19:47 ermek $
 * @author     Ermek
 */

/**
 * @category   Application_Extensions
 * @package    Quiz
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Engine_Form_Decorator_QuizQuestion extends Zend_Form_Decorator_Abstract
{
  /**
   * Default placement: surround content
   * @var string
   */
  protected $_placement = null;

  /**
   * Render
   *
   * Renders as the following:
   * <dt></dt>
   * <dd>$content</dd>
   *
   * @param  string $content
   * @return string
   */
  public function render($content)
  {
    $label = $this->getOption('label');
    $number = $this->getOption('number');
    $photo = $this->getOption('photo');
    
    return '<div class="quiz_question float_right_rtl">'
      . '<div class="quiz_question_label"><span class="quiz_question_number">' . $number . '</span>' . $label . '</div>'
      . '<div class="quiz_question_photo">' . $photo . '</div>'
      . '<div class="quiz_question-answers">' . $content . '</div>'
      . '</div>';
  }
}