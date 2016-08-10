<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: faq.tpl  16.01.12 15:17 TeaJay $
 * @author     Taalay
 */
?>

<script type="text/javascript">
  var openAnswer = function(id) {
    var display = $('faq_closedArrow_'+id).getStyle('display');
    if (display == 'block') {
      $('faq_snippet_'+id).setStyle('display', 'none');
      $('faq_closedArrow_'+id).setStyle('display', 'none');
      $('faq_openArrow_'+id).setStyle('display', display);
      $('faq_answer_'+id).setStyle('display', display);
    } else {
      $('faq_openArrow_'+id).setStyle('display', display);
      $('faq_answer_'+id).setStyle('display', display);
      $('faq_snippet_'+id).setStyle('display', 'block');
      $('faq_closedArrow_'+id).setStyle('display', 'block');
    }
  }
</script>

<div class="headline">
  <h2>
    <?php echo $this->translate('Credits');?>
  </h2>
  <?php if( count($this->navigation) > 0 ): ?>
    <div class="tabs">
      <?php
        // Render the menu
        echo $this->navigation()
          ->menu()
          ->setContainer($this->navigation)
          ->render();
      ?>
    </div>
  <?php endif; ?>
</div>

<div class="phs">
  <div>
    <?php foreach($this->faqs as $key => $faq) : ?>
      <div>
        <div class="credit_qa_container">
          <div class="faq_arrow">
            <span class="faq_closedArrow" id="faq_closedArrow_<?php echo $key?>">
              <a onclick="openAnswer('<?php echo $key?>')">
                <i class="faq_closedArrow_icon img"></i>
              </a>
            </span>
            <span class="faq_openArrow" id="faq_openArrow_<?php echo $key?>">
              <a onclick="openAnswer('<?php echo $key?>')">
                <i class="faq_openArrow_icon img"></i>
              </a>
            </span>
          </div>
          <div class="faq_content">
            <div class="credit_faq_question">
              <a onclick="openAnswer('<?php echo $key?>')">
                <?php echo $this->translate($faq['q']); ?>
              </a>
            </div>
            <div class="faq_snippet" id="faq_snippet_<?php echo $key?>">
              <?php echo $this->string()->truncate(Engine_String::strip_tags($this->translate($faq['a'])), 100, '...')?>
            </div>
            <div class="faq_answer" id="faq_answer_<?php echo $key?>">
              <div>
                <?php echo $this->translate($faq['a'])?>
              </div>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<div class="credit_table_form credit_faq_table" id="faq_credits">
  <p style="margin-bottom: 10px;"><?php echo $this->translate('CREDIT_FAQ_ACTION_TYPES_DESCRIPTION')?></p>
  <table class='credit_table'>
    <thead>
      <tr>
        <th><?php echo $this->translate("Action Type") ?></th>
        <th><?php echo $this->translate("Credit") ?></th>
        <th><?php echo $this->translate("Max Credit") ?></th>
        <th><?php echo $this->translate("Rollover Period") ?></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach( $this->actionTypes as $key => $type): ?>
        <?php if ($key): ?>
          <?php $is_module = ($type->action_module != $this->actionTypes[$key-1]->action_module); $type = $this->actionTypes[$key];?>
          <?php if ($is_module): ?>
            <tr><td colspan="4" class="credit_faq_module"><?php echo ucfirst($this->translate('_CREDIT_'.$type->action_module)); ?></td></tr>
          <?php endif; ?>
        <?php else : ?>
          <tr><td colspan="4" class="credit_faq_module"><?php echo ucfirst($this->translate('_CREDIT_'.$type->action_module)); ?></td></tr>
        <?php endif; ?>
        <tr>
          <td><?php echo $this->translate('_CREDIT_ACTION_TYPE_' . strtoupper(trim(preg_replace('/[^a-zA-Z0-9]+/', '_', $type->action_type), '_')))?></td>
          <td style="text-align: center"><?php echo $this->locale()->toNumber($type->credit) ?></td>
          <td style="text-align: center"><?php echo $this->locale()->toNumber($type->max_credit) ?></td>
          <td style="text-align: center"><?php echo ($type->rollover_period) ? $this->locale()->toNumber($type->rollover_period) . $this->translate(' day(s)') : $this->translate('never')?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>