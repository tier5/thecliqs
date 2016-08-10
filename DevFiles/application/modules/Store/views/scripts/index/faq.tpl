<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: faq.tpl  30.04.12 16:04 TeaJay $
 * @author     Taalay
 */
?>

<script type="text/javascript">
  var openAnswer = function(id) {
    var display = $('store_faq_closedArrow_'+id).getStyle('display');
    if (display == 'block') {
      $('store_faq_snippet_'+id).setStyle('display', 'none');
      $('store_faq_closedArrow_'+id).setStyle('display', 'none');
      $('store_faq_openArrow_'+id).setStyle('display', display);
      $('store_faq_answer_'+id).setStyle('display', display);
    } else {
      $('store_faq_openArrow_'+id).setStyle('display', display);
      $('store_faq_answer_'+id).setStyle('display', display);
      $('store_faq_snippet_'+id).setStyle('display', 'block');
      $('store_faq_closedArrow_'+id).setStyle('display', 'block');
    }
  }
</script>

<?php echo $this->content()->renderWidget('store.navigation-tabs'); ?>

<div class="store_faq">
  <div>
    <?php foreach($this->faqs as $key => $faq) : ?>
      <div>
        <div class="store_qa_container">
          <div class="store_faq_arrow">
            <span class="store_faq_closedArrow" id="store_faq_closedArrow_<?php echo $key?>">
              <a onclick="openAnswer('<?php echo $key?>')">
                <i class="store_faq_closedArrow_icon img"></i>
              </a>
            </span>
            <span class="store_faq_openArrow" id="store_faq_openArrow_<?php echo $key?>">
              <a onclick="openAnswer('<?php echo $key?>')">
                <i class="store_faq_openArrow_icon img"></i>
              </a>
            </span>
          </div>
          <div class="store_faq_content">
            <div class="store_faq_question">
              <a onclick="openAnswer('<?php echo $key?>')">
                <?php echo $faq->question; ?>
              </a>
            </div>
            <div class="store_faq_snippet" id="store_faq_snippet_<?php echo $key?>">
              <?php echo $this->string()->truncate(Engine_String::strip_tags($faq->answer), 200, '...')?>
            </div>
            <div class="store_faq_answer" id="store_faq_answer_<?php echo $key?>">
              <div>
                <?php echo $faq->answer; ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>