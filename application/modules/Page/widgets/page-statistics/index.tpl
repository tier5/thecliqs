<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl  14.11.11 11:07 TeaJay $
 * @author     Taalay
 */
?>
<div class="page_statistics global_form_box">
  <table width="100%" cellpadding="0" cellspacing="0">
    <tbody>
      <tr>
        <td class="label"><?php echo $this->translate("Unique Visitors"); ?> *</td>
        <td class="value"><?php echo $this->subject->getTotalVisitorsCount(); ?></td>
      </tr>
      <tr>
        <td class="label"><?php echo $this->translate("Page Views"); ?> *</td>
        <td class="value"><?php echo $this->subject->getTotalViewsCount(); ?></td>
      </tr>
    </tbody>
  </table>
  <div>* <?php echo $this->translate("Data for last 30 days"); ?></div>
  <?php if ($this->isAdmin) : ?>
    <?php echo $this->htmlLink($this->url(array('page_id' => $this->subject->getIdentity()), 'page_stat'), $this->translate('View Detailed Statistics')); ?>
  <?php endif; ?>
</div>