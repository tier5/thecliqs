<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Quiz
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: quizJsTabs.tpl 2010-07-02 18:20 ermek $
 * @author     Ermek
 */
?>

<ul id="main_tabs">
  <?php foreach( $this->container as $link ): ?>
    <li class="<?php echo ( $link->isActive() ? 'active' : '' ); ?>">
      <a href="javascript://" class="<?php echo $link->getClass(); ?>" id="menu-<?php echo $link->getId() ?>" ><?php echo $link->getLabel() ?></a>
    </li>
  <?php endforeach; ?>
</ul>