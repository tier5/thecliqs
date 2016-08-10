<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Timeline
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: albums.tpl 2012-02-01 16:58:20 mt.uulu $
 * @author     Mirlan
 */

?>

<?php if ($this->just_items): ?>
<?php echo $this->render('photo/_albums.tpl'); ?>
<?php else: ?>
<script type="text/javascript">
  var photos = {
    he_list:null,
    loader:null,
    load_page:function (page) {
      var self = this;
      new Request.HTML({
        'method':'get',
        'data':{'format':'html', 'page':page, 'just_items':'1'},
        'url':'<?php echo $this->url(array(
          'id' => $this->user->getIdentity(),
          'type' => $this->type,
        ), 'timeline_photo', true); ?>/page/' + page,
        'onRequest':function () {
          self.he_list.toggleClass('hidden');
          self.loader.toggleClass('hidden');
        },
        'onComplete':function (responseTree, responseElements, responseHTML, responseJavaScript) {
          self.he_list.set('html', responseHTML);
          self.loader.toggleClass('hidden');
          self.he_list.toggleClass('hidden');
        }
      }).send();
    }
  }

  window.addEvent('domready', function () {
    photos.he_list = $('he_list');
    photos.loader = $('he_contacts_loading');
  });
</script>

<div class='tl-photos'>
  <div id="he_contacts_loading" class="hidden">&nbsp;</div>
  <div class="he_contacts">
    <h4 class="contacts_header"><?php echo $this->translate('TIMELINE_Choose from your Photos');?></h4>
    <?php if ($this->paginator->getCurrentItemCount() > 0): ?>
    <div class="options" style="padding-right: 20px">
      <div class="select_btns" style='width: 100%'>
        <a href="javascript:void(0)">
          <?php echo $this->translate("TIMELINE_Select Album"); ?>
        </a>
        <!--      <a href="javascript:void(0)" style="float:right;">
          <?php /*echo $this->translate("TIMELINE_View Albums"); */?>
        </a>-->
      </div>
      <div class="clr"></div>
    </div>
    <?php endif; ?>

    <div class="clr"></div>
    <div class="contacts">
      <div id="he_list">

        <?php echo $this->render('photo/_albums.tpl'); ?>

        <div class="clr" id="he_contacts_end_line"></div>
      </div>
      <div class="clr"></div>

    </div>
    <div class="clr"></div>
  </div>
</div>
<?php endif; ?>