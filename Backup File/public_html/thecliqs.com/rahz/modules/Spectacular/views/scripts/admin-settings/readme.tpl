<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Spectacular
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: readme.tpl 2015-06-04 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<h2>
    <?php echo "Responsive Spectacular Theme"; ?>
</h2>
<div class="seaocore_admin_tabs">
    <ul class="navigation">
        <li class="active">
            <a href="<?php echo $this->baseUrl() . '/admin/spectacular/settings/readme' ?>" ><?php echo 'Please go through these important points and proceed by clicking the button at the bottom of this page.'; ?></a>

        </li>
    </ul>
</div>

<?php include_once APPLICATION_PATH . '/application/modules/Spectacular/views/scripts/admin-settings/faq_help.tpl'; ?>
<br />
<button onclick="form_submit();"><?php echo 'Proceed to enter License Key'; ?> </button>

<script type="text/javascript" >
    function form_submit() {
        var url = '<?php echo $this->url(array('module' => 'spectacular', 'controller' => 'settings'), 'admin_default', true) ?>';
        window.location.href = url;
    }
</script>