<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Daylogo
 * @copyright  Copyright Hire-Experts
 * @license    http://www.hire-experts.com
 * @version    $Id: create.tpl 2012-08-16 16:40 nurmat $
 * @author     Nurmat
 */

$this->headScript()
    ->appendFile( $this->layout()->staticBaseUrl . 'application/modules/Daylogo/externals/scripts/Daylogo.js');

$this->headTranslate(array(
    'DAYLOGO_CREATE_TITLE'
));
?>

<script type="text/javascript">
    en4.core.runonce.add(function (){
        Daylogo.url.form = '<?php echo $this->url(array('action' => 'create'), 'daylogo_admin_index')?>';
        Daylogo.url.remove_photo = '<?php echo $this->url(array('action' => 'remove-photo'), 'daylogo_admin_index')?>';
        Daylogo.init();
    });
</script>

<h2>
    <?php echo $this->translate('Daylogo Plugin') ?>
</h2>

<?php if( count($this->navigation) ): ?>
<div class='tabs'>
    <?php
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
</div>
<?php endif; ?>

<div class="daylogo_loader hidden" id="daylogo_loader">
    <?php echo $this->htmlImage( $this->baseUrl() . '/application/modules/Daylogo/externals/images/loader.gif'); ?>
</div>
<div>&nbsp;</div>
<div class="daylogo" id="daylogo">
    <div class="tab_form tab">
        <?php echo $this->form->render($this); ?>
    </div>
    <div class="tab_message hidden tab"></div>
</div>