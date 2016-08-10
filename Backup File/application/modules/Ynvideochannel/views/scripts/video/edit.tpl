<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideochannel
 * @author     YouNet Company
 */
?>

<script type="text/javascript">
    window.addEvent('domready', function() {
        $('category_id').addEvent('change', function () {
            $(this).getParent('form').submit();
        });

        if ($('0_0_1-wrapper')) {
            $('0_0_1-wrapper').setStyle('display', 'none');
        }
    });
</script>

<?php
    echo $this->form->render();
?>
