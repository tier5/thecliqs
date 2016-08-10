<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynfilesharing
 * @author     YouNet Company
 */
?>
<?php
	echo $this->partial('_script_tags.tpl', 'ynfilesharing'); 
?>
<div class="headline">
    <h2>
        <?php echo $this->translate('File Sharing'); ?>
    </h2>
    <div class="tabs">
        <?php
        // Render the menu
        echo $this->navigation()
                ->menu()
                ->setContainer($this->navigation)
                ->render();
        ?>
    </div>
</div>

<?php echo $this->form->render($this); ?>