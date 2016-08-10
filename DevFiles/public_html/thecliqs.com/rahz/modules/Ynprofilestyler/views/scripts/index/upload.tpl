<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynprofilestyler
 * @author     YouNet Company
 */
?>

<?php if (!empty($this->url)) : ?>
	<script language="javascript" type="text/javascript">
		window.opener.ynps2.setValueForCurrentRule('<?php echo $this->url?>');
		window.close();
	</script>
<?php endif; ?>
<?php echo $this->form->render($this) ?>
<style type="text/css">
.global_form {
    clear: both;
    overflow: hidden;
}
ul.errors
{
    margin: 0px 0px 20px 0px;
    overflow: hidden;
    list-style-type: none;
    padding-left: 0;
}
ul.errors > li
{
    border-radius: 3px;
    margin: 7px 5px 7px 5px;
    padding: 5px 15px 5px 32px;
    background-repeat: no-repeat;
    background-position: 8px 5px;
    float: left;
    clear: left;
    overflow: hidden;
    background-image: url(../../application/modules/Core/externals/images/error.png);
    background-color: #f5f0db;
    border: none;
}
ul {
    list-style-type: none;
}
ul.errors > li > ul > li {
    font-size: 1em;
    font-weight: bold;
}
.form-elements {
    padding: .7em;
}
</style>