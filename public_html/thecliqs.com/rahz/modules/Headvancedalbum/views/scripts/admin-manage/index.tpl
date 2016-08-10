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
<script type="text/javascript">
    function multiDelete() {
        return confirm("<?php echo $this->translate('Are you sure you want to delete the selected photo albums?');?>");
    }

    function selectAll() {
        var i;
        var multidelete_form = $('multidelete_form');
        var inputs = multidelete_form.elements;
        for (i = 1; i < inputs.length - 1; i++) {
            inputs[i].checked = inputs[0].checked;
        }
    }

    function set_album_featured(album_id) {
        new Request.JSON({
            'method':'post',
            'data':{'format':'json', 'album_id':album_id},
            'url':'<?php echo $this->url(array(
                'module' => 'headvancedalbum',
                'controller' => 'manage',
                'action' => 'set-album'
            ), 'admin_default', true); ?>',
            'onRequest':function () {},
            'onSuccess':function (response) {
                if (response.status) {
                    $('featured_icon_'+album_id).set('src', '<?php echo $this->layout()->staticBaseUrl;?>application/modules/Headvancedalbum/externals/images/featured'+response.result+'.png');
                }
            }
        }).send();
    }
</script>

<h2>
    <?php echo $this->translate("HE Advanced Albums Plugin") ?>
</h2>

<?php if (count($this->navigation)): ?>
<div class='tabs'>
    <?php
    // Render the menu
    //->setUlClass()
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
</div>
<?php endif; ?>

<p>
    <?php echo $this->translate("ALBUM_VIEWS_SCRIPTS_ADMINMANAGE_INDEX_DESCRIPTION") ?>
</p>
<?php
$settings = Engine_Api::_()->getApi('settings', 'core');
if ($settings->getSetting('user.support.links', 0) == 1) {
    echo 'More info: <a href="http://support.socialengine.com/questions/186/Admin-Panel-Plugins-Photo-Albums" target="_blank">See KB article</a>.';
}
?>
<br/>
<br/>
<?php if (count($this->paginator)): ?>

<form id="multidelete_form" action="<?php echo $this->url();?>" onSubmit="return multiDelete()" method="POST">
    <table class='admin_table'>
        <thead>
        <tr>
            <th class='admin_table_short'><input onclick="selectAll()" type='checkbox' class='checkbox'/></th>
            <th class='admin_table_short'>ID</th>
            <th><?php echo $this->translate('Title') ?></th>
            <th><?php echo $this->translate('Owner') ?></th>
            <th><?php echo $this->translate('Views') ?></th>
            <th><?php echo $this->translate('Date') ?></th>
            <th><?php echo $this->translate('Options') ?></th>
        </tr>
        </thead>
        <tbody>
            <?php foreach ($this->paginator as $item): ?>
        <tr>
            <td><input type='checkbox' class='checkbox' name='delete_<?php echo $item->album_id;?>'
                       value="<?php echo $item->album_id ?>"/></td>
            <td><?php echo $item->getIdentity() ?></td>
            <td><?php echo $item->getTitle(); ?></td>
            <td>
                <a href="<?php echo $this->user($item->owner_id)->getHref(); ?>">
                    <?php echo $this->user($item->owner_id)->getTitle() ?>
                </a>
            </td>
            <td><?php echo $this->locale()->toNumber($item->view_count) ?></td>
            <td><?php echo $this->locale()->toDateTime($item->creation_date) ?></td>
            <td>
                <a class="he_advanced_album_admin_icon smoothbox"
                   href="<?php echo $this->url(array('module' => 'headvancedalbum', 'controller' => 'manage', 'action' => 'photos', 'album_id' => $item->getIdentity()), 'admin_default'); ?>">
                    <img src="<?php echo $this->layout()->staticBaseUrl;?>application/modules/Headvancedalbum/externals/images/view.png"
                         alt="<?php echo $this->translate("View Photos"); ?>"
                         title="<?php echo $this->translate("View Photos"); ?>">
                </a>
                <a class="he_advanced_album_admin_icon"
                   onclick="set_album_featured('<?php echo $item->getIdentity(); ?>');"
                   href="javascript://">
                    <img id="featured_icon_<?php echo $item->getIdentity(); ?>"
                         src="<?php echo $this->layout()->staticBaseUrl;?>application/modules/Headvancedalbum/externals/images/featured<?php echo $item->he_featured; ?>.png"
                         alt="<?php echo $this->translate("featured"); ?>"
                         title="<?php echo $this->translate("featured"); ?>">
                </a>
                <a class="he_advanced_album_admin_icon smoothbox"
                   href="<?php echo $this->url(array('module' => 'album', 'controller' => 'admin-manage', 'action' => 'delete', 'id' => $item->album_id), 'default');?>">
                    <img src="<?php echo $this->layout()->staticBaseUrl;?>application/modules/Headvancedalbum/externals/images/delete.png"
                         alt="<?php echo $this->translate("Delete"); ?>"
                         title="<?php echo $this->translate("Delete"); ?>">
                </a>
            </td>
        </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <br/>

    <div class='buttons'>
        <button type='submit'>
            <?php echo $this->translate('Delete Selected') ?>
        </button>
    </div>
</form>

<br/>

<div>
    <?php echo $this->paginationControl($this->paginator); ?>
</div>

<?php else: ?>
<div class="tip">
    <span>
      <?php echo $this->translate("There are no albums posted by your members yet.") ?>
    </span>
</div>
<?php endif; ?>