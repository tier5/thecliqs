<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Headvancedalbum
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: add.tpl 2013-01-21 15:31 ratbek $
 * @author     Ratbek
 */
?>
<?php if ($this->just_items): ?>
<?php echo $this->render('admin-manage/_photos.tpl'); ?>
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
                    'module' => 'headvancedalbum',
                    'controller' => 'manage',
                    'action' => 'photos',
                    'id' => $this->owner_id,
                    'type' => $this->type,
                ), 'admin_default', true); ?>/page/' + page + '/album_id/' + '<?php echo $this->album->getIdentity(); ?>',
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
        },

        set_photo_featured:function (photo_id) {
            var self = this;
            new Request.JSON({
                'method':'post',
                'data':{'format':'json', 'photo_id':photo_id},
                'url':'<?php echo $this->url(array(
                    'module'=>'headvancedalbum',
                    'controller'=>'manage',
                    'action' => 'set-photo'
                ), 'admin_default', true); ?>',
                'onRequest':function () {},
                'onSuccess':function (response) {
                    if (response.status) {
                        $('featured_'+photo_id).set('src', '<?php echo $this->layout()->staticBaseUrl;?>application/modules/Headvancedalbum/externals/images/featured'+response.result+'.png');
                    }
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
        <h4 class="contacts_header"><?php echo $this->translate('HEADVANCEDALBUM_Choose from your Photos');?></h4>
        <?php if ($this->paginator->getCurrentItemCount() > 0): ?>
        <div class="options" style="padding-right: 20px">
            <div class="select_btns" style='width: 100%'>
                <?php echo $this->album->getTitle(); ?>
            </div>
            <div class="clr"></div>
        </div>
        <?php endif; ?>

        <div class="clr"></div>
        <div class="contacts">
            <div id="he_list">

                <?php echo $this->render('admin-manage/_photos.tpl'); ?>

                <div class="clr" id="he_contacts_end_line"></div>
            </div>
            <div class="clr"></div>

        </div>
        <div class="clr"></div>
    </div>
</div>
<?php endif; ?>