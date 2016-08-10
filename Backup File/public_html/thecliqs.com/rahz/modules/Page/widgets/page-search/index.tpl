<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl:  04.11.11 18:07 taalay $
 * @author     Taalay
 */

$this->headTranslate(array(
  'Search'
));

?>

<script type="text/javascript">
  en4.core.runonce.add(function(){
    page_manager.init();
        var isMultiMode = <?php echo($this->isMultiMode);?>;

        if(!isMultiMode){
            return;
        }

        if($('set').get('value') == 0) {
            $('category-container').hide();
        }

        $('set').addEvent('change', function(){
            if(this.value == 0) {
                $('category-container').hide();
                $('profile_type').empty().grab(new Element('option'));
                return;
            }
            var set = <?php echo ($this->setInfoJSON) ? $this->setInfoJSON : $this->setInfoJSON; ?>;
            $('profile_type').empty().grab(new Element('option'));
            changeFields($('profile_type').set('value', 0));
            var o = this;
            (new Hash(set[this.value]['items'])).each(function(item, i){
                var option = new Element('option', {'label':item['caption'], 'value':i});
                option.appendText(item['caption']);
                $('profile_type').grab(option);
            });
            if($('category-container').getStyle('display') == "none")
                $('category-container').show();
        });
  });
</script>
<?php echo $this->filterForm->render($this); ?>

<?php
  /* Include the common user-end field switching javascript */
  echo $this->partial('_jsSwitch.tpl', 'page', array(
'topLevelId' => (int) @$this->topLevelId,
'topLevelValue' => (int) @$this->topLevelValue
))
?>