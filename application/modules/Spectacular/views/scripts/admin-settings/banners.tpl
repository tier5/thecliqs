<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Spectacular
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: banners.tpl 2015-06-04 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$spectacularThemeActivated = true;
$themeInfo = Zend_Registry::get('Themes', null);
if (!empty($themeInfo)):
    foreach ($themeInfo as $key => $value):
        if ($key != "spectacular"):
            $spectacularThemeActivated = false;
        endif;
    endforeach;
endif;

if ((Engine_Api::_()->getApi('settings', 'core')->getSetting('spectacular.isActivate', 0)) && empty($spectacularThemeActivated)):
    ?>
    <div class="seaocore_tip">
        <span>
            <?php echo "Please activate the 'Spectacular Theme' from 'Layout' >> 'Theme Editor', available in the admin panel of your site." ?>
        </span>
    </div>
<?php endif; ?>

<script type="text/javascript">

    var SortablesInstance;
    window.addEvent('load', function () {
        SortablesInstance = new Sortables('menu_list', {
            clone: true,
            constrain: false,
            handle: '.item_label',
            onComplete: function (e) {
                reorder(e);
            }
        });
    });

    var reorder = function (e) {
        var menuitems = e.parentNode.childNodes;
        var ordering = {};
        var i = 1;
        for (var menuitem in menuitems)
        {
            var child_id = menuitems[menuitem].id;

            if ((child_id != undefined))
            {
                ordering[child_id] = i;
                i++;
            }
        }
        ordering['format'] = 'json';

        // Send request
        var url = '<?php echo $this->url(array('action' => 'order-banners')) ?>';
        var request = new Request.JSON({
            'url': url,
            'method': 'POST',
            'data': ordering,
            onSuccess: function (responseJSON) {
            }
        });

        request.send();
    }
</script>

<h2>
    <?php echo 'Responsive Spectacular Theme' ?>
</h2>

<div class='tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
</div>

<h3><?php echo $this->translate('Banner Images'); ?></h3>
<p class="form-description">
    <?php echo $this->translate("This theme enables you to have banners on the desired widgetized pages of your website. Banners enable you to make your website's pages look attractive by enhancing their visual appeal, and are also good for Search Engine Optimization (SEO). Below you can upload / delete / manage all your banner images. You can also set the sequence of banner images by dragging-and-dropping them vertically. Multiple banner images can be added to display them in a circular manner, i.e one after another. For pages where you want to show banners, place the 'Responsive Spectacular Theme - Banner Images' widget, and configure its settings like: Title, Description, images to show, etc."); ?>
</p>

<br />
<p>
    <a href='<?php echo $this->url(array("module" => "spectacular", "controller" => "settings", "action" => 'add-banners'), "admin_default", true) ?>' class="smoothbox buttonlink seaocore_icon_add"><?php echo $this->translate("Add New Banner Image"); ?></a>
</p>

<br />

<?php if (COUNT($this->list)): ?>
    <form id='multidelete_form' method="post" action="<?php echo $this->url(array('action' => 'multi-delete-banners')); ?>" onSubmit="return multiDelete()">
        <div class="seaocore_admin_order_list">
            <div class="list_head">
                <div class="center" style="width:2%;text-align:center;">
                    <input onclick="selectAll()" type='checkbox' class='checkbox'>
                </div>
                <div class="center" style="width:5%;text-align:center;">
                    <?php echo "Id"; ?>
                </div>

                <div style="width:15%">
                    <?php echo "Image Name"; ?>
                </div>

                <div class="center" style="width:30%;text-align:center;">
                    <?php echo "Banner Images"; ?>
                </div>

                <div class="center" style="width:8%;text-align:center;">
                    <?php echo "Enabled"; ?>
                </div>

                <div class="center" style="width:8%;text-align:center;">
                    <?php echo "Order"; ?>
                </div>

                <div class="center" style="width:8%;text-align:center;">
                    <?php echo "Options"; ?>
                </div>
            </div>
            <ul id='menu_list'>
                <?php foreach ($this->list as $item): ?>
                    <li id="content_<?php echo $item->getIdentity(); ?>" class="admin_table_bold item_label">
                        <input type='hidden'  name='order[]' value='<?php echo $item->getIdentity(); ?>'>
                        <div class="center" style="width:2%;text-align:center;">
                            <input name='delete_<?php echo $item->getIdentity(); ?>' type='checkbox' class='checkbox' value="<?php echo $item->getIdentity() ?>"/>
                        </div>
                        <div class="center" style="width:5%;text-align:center;">
                            <?php echo $item->getIdentity(); ?>
                        </div>

                        <div style="width:15%">
                            <?php echo $item->getTitle(); ?>
                        </div>

                        <div class="center" style="width:30%;text-align:center;">
                            <?php
                            $iconSrc = Engine_Api::_()->spectacular()->displayPhoto($item->icon_id, 'thumb.icon');
                            if (!empty($iconSrc)):
                                ?>
                                <img src="<?php echo $iconSrc; ?>" />
                            <?php endif; ?>
                        </div>
                        <div class="center" style="width:8%;text-align:center;">
                            <a href='<?php echo $this->url(array("module" => "spectacular", "controller" => "settings", "action" => 'enabled-banners', 'id' => $item->getIdentity()), "admin_default", true) ?>' >
                                <?php if (!empty($item->enabled)): ?>
                                    <img src="<?php echo $this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/approved.gif' ?>" alt="" title="Make Disabled">
                                <?php else: ?>
                                    <img src="<?php echo $this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/disapproved.gif' ?>" alt="" title="Make Enabled">
                                <?php endif; ?></a>
                        </div>

                        <div class="center" style="width:8%;text-align:center;">
                            <?php echo $item->order; ?>
                        </div>

                        <div class="center" style="width:8%;text-align:center;">
                            <a href='<?php echo $this->url(array("module" => "spectacular", "controller" => "settings", "action" => 'delete-banners', 'id' => $item->getIdentity()), "admin_default", true) ?>' class="smoothbox"><?php echo "Delete"; ?></a>
                        </div>

                    <?php endforeach; ?>
            </ul>
        </div>
        <br />
        <div class='buttons'>
            <button type='submit'><?php echo $this->translate('Delete Selected'); ?></button>
        </div>
    </form>
<?php else: ?>
    <div class="tip" style="width: 100%">
        <span style="width: 100%"><?php echo $this->translate('No banners Found!') ?></span>
    </div>
<?php endif; ?>

<script type="text/javascript">


    function multiDelete()
    {
        return confirm('<?php echo $this->string()->escapeJavascript($this->translate("Are you sure you want to delete selected banners ?")) ?>');
    }

    function selectAll()
    {
        var i;
        var multidelete_form = $('multidelete_form');
        var inputs = multidelete_form.elements;
        for (i = 1; i < inputs.length - 1; i++) {
            if (!inputs[i].disabled) {
                inputs[i].checked = inputs[0].checked;
            }
        }
    }
</script>
