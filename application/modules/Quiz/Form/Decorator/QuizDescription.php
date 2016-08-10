<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Quiz
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: QuizDescription.php 2010-07-02 19:47 ermek $
 * @author     Ermek
 */

/**
 * @category   Application_Extensions
 * @package    Quiz
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Engine_Form_Decorator_QuizDescription extends Zend_Form_Decorator_Abstract
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

    return '<div class="form-wrapper">'
      . '<div class="form-label">' . $label . '</div>'
      . '<div class="form-element">' . $content . '</div>'
      . '</div>';
  }
}