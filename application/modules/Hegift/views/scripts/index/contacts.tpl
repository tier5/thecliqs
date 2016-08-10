<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: contacts.tpl  09.02.12 14:03 TeaJay $
 * @author     Taalay
 */
?>

<?php if ($this->error): ?>
  <div class="contacts_error"><?php echo $this->message; ?></div>
<?php else: ?>
  <?php
    $gift = $this->item('gift', $this->params['gift_id']);
    $balance = Engine_Api::_()->getItem('credit_balance', Engine_Api::_()->user()->getViewer()->getIdentity());
    $current_balance = ($balance) ? $balance->current_credit : 0;
  ?>
  <script type="text/javascript">

    window.HE_CONTACTS = null;
    var options = {
      c: "<?php echo $this->callback; ?>",
      listType: "all",
      m: "<?php echo $this->module; ?>",
      l: "<?php echo $this->list; ?>",
      ipp: <?php echo (int)$this->ipp; ?>,
      p: <?php echo (int)$this->items->getCurrentPageNumber(); ?>,
      total: <?php echo (int)$this->items->getTotalItemCount(); ?>,
      params: <?php echo Zend_Json::encode($this->params); ?>,
      nli: <?php echo (int)$this->not_logged_in; ?>,
      contacts: <?php echo Zend_Json_Encoder::encode($this->checkedItems); ?>
    };

    window.HE_CONTACTS = new HEContacts(options);

    function prepare_form() {
      if ($form = $('hegift_send_form_message_and_privacy')) {
        gift_manager.message = $form.message.value;
        if ($form.privacy[1].checked) {
          gift_manager.privacy = 0;
        }
      }
    };

    function check_and_count(id) {
      var owner_id = <?php echo $gift->owner_id?>;
      var credits, amount;
      if (owner_id || $('adding_removing_price')) {
        if ($('contact_'+id).hasClass('active')) {
          $('current_credit').innerHTML = parseFloat($('current_credit').innerHTML) + parseFloat('<?php echo $gift->credits?>');
          $('adding_removing_price').innerHTML = parseFloat($('adding_removing_price').innerHTML) - parseFloat('<?php echo $gift->credits?>');
        } else {
          credits = parseFloat($('current_credit').innerHTML) - parseFloat('<?php echo $gift->credits?>');
          if (credits < 0) {
            gift_manager.contacts.chooseContact($('contact_'+id));
          } else {
            $('current_credit').innerHTML = credits;
            $('adding_removing_price').innerHTML = parseFloat($('adding_removing_price').innerHTML) + parseFloat('<?php echo $gift->credits?>');
          }
        }
      } else {
        if ($('contact_'+id).hasClass('active')) {
          $('current_credit').innerHTML = parseFloat($('current_credit').innerHTML) + parseFloat('<?php echo $gift->credits?>');
          $('amount_of_gift').innerHTML = parseInt($('amount_of_gift').innerHTML ) + parseInt(1);
        } else {
          credits = parseFloat($('current_credit').innerHTML) - parseFloat('<?php echo $gift->credits?>');
          amount = parseInt($('amount_of_gift').innerHTML) - parseInt(1);
          if (credits < 0 || amount < 0) {
            gift_manager.contacts.chooseContact($('contact_'+id));
          } else {
            $('current_credit').innerHTML = credits;
            $('amount_of_gift').innerHTML = amount;
          }
        }
      }
    }

    window.addEvent('domready', function() {
      if (gift_manager.contacts.options.params.user_id == 0) {
        gift_manager.contacts.needPagination = '<?php echo $this->need_pagination;?>';
      }
    });
  </script>

<div id="he_contacts_loading" style="display:none;">&nbsp;</div>
<div id="he_contacts_message" style="display:none;"><div class="msg"></div></div>

<div class="he_contacts" id="giftsSendPopup">
  <h4 class="contacts_header"><?php echo $this->translate($this->title); ?></h4>

  <div id="giftsSendPopup_content" class="gift_send_content">
    <div id="giftsSendPopup_img" class="img_gift" style="background-image: url('<?php echo $this->layout()->staticBaseUrl?>application/modules/User/externals/images/nophoto_user_thumb_profile.png');">
      <img src="<?php echo $gift->getPhotoUrl('thumb.icon') ?>"/>
    </div>
    <form method="post" id="hegift_send_form_message_and_privacy">
      <div class="form_send">
        <div class="message_header">
          <?php echo $this->translate('HEGIFT_Your Message (150 symbols):')?>
        </div>
        <textarea id="giftsSendPopup_message" name="message" maxlength="150"></textarea>
        <table class="checked_table">
          <tbody>
            <tr>
              <td>
                <input type="radio" id="giftsSendPopup_giftPublic" checked="checked" value="0" name="privacy">
              </td>
              <td>
                <label for="giftsSendPopup_giftPublic">
                  <b><?php echo $this->translate('HEGIFT_Public:')?></b>
                  <span><?php echo $this->translate('HEGIFT_Your name and message will be available to all')?></span>
                </label>
              </td>
            </tr>
            <tr>
              <td><input type="radio" id="giftsSendPopup_giftPrivate" value="1" name="privacy"></td>
              <td>
                <label for="giftsSendPopup_giftPrivate">
                  <b><?php echo $this->translate('HEGIFT_Private:')?></b>
                  <span><?php echo $this->translate('HEGIFT_Your name and message visible only to the gift recipient')?></span>
                </label>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </form>
  </div>

  <?php if (isset($this->items) && $this->items->getCurrentItemCount() > 0): ?>
    <div class="options">
      <div class="select_btns">
        <a href="javascript:void(0)" id="he_contacts_list_all" class="active">
          <?php echo $this->translate("HEGIFT_All"); ?>
        </a>
        <a href="javascript:void(0)" id="he_contacts_list_selected">
          <?php echo $this->translate("HEGIFT_Selected"); ?>
        </a>
      </div>
      <div class="contacts_filter">
        <div class="list_filter_cont">
          <input type="text" class="list_filter" title="Search" id="contacts_filter" name="q" />
          <a class="list_filter_btn" id="contacts_filter_submit" title="Search" href="javascript://"></a>
        </div>
      </div>

      <div class="clr"></div>
    </div>
    <div class="clr"></div>
  <?php endif; ?>

  <div class="contacts">
    <div id="he_contacts_list">
      <?php echo $this->render('_contacts_items.tpl'); ?>
    </div>

    <?php if ($this->items->count() > $this->items->getCurrentPageNumber()): ?>
      <div class="clr"></div>
      <a class="pagination" id="contacts_more" href="javascript:void(0);"><?php echo $this->translate("HEGIFT_More"); ?></a>
    <?php endif; ?>

    <div class="clr"></div>
  </div>
  <div class="clr"></div>

  <div class="btn">
    <button id="submit_contacts" style="float:left;" onclick="prepare_form()"><?php echo $this->translate((isset($this->params['button_label']))?$this->params['button_label']:"Send"); ?></button>
  </div>
  <div style="float: right;">
    <?php echo $this->translate('HEGIFT_You have')?><span id="current_credit" style="margin: 0 5px; color: blue; font-weight: bold"><?php echo $current_balance?></span><?php echo $this->translate('credits')?>,
    <?php if (!$gift->owner_id && $gift->amount !== null) : ?>
      <span id="amount_of_gift" style="margin: 0 5px; color: #8a2be2; font-weight: bold"><?php echo $gift->amount?></span><?php echo $this->translate('HEGIFT_gifts left')?>
    <?php else : ?>
      <?php echo $this->translate('HEGIFT_Price')?><span id="adding_removing_price" style="margin: 0 5px; color: #8a2be2; font-weight: bold">0</span><?php echo $this->translate('HEGIFT_credits')?>
    <?php endif; ?>
  </div>
</div>

<?php endif; ?>
