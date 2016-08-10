<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: save-file.tpl  16.12.11 16:41 ulan t $
 * @author     Ulan T
 */
?>

<?php
$this->headScript()
  ->appendFile($this->layout()->staticBaseUrl.'externals/autocompleter/Observer.js')
  ->appendFile($this->layout()->staticBaseUrl.'externals/autocompleter/Autocompleter.js')
  ->appendFile($this->layout()->staticBaseUrl.'externals/autocompleter/Autocompleter.Local.js')
  ->appendFile($this->layout()->staticBaseUrl.'externals/autocompleter/Autocompleter.Request.js');
?>

<script type="text/javascript">
    en4.core.runonce.add(function()
    {
        pageAutocomplete = new Autocompleter.Request.JSON('username', '<?php echo $this->url(array('module' => 'page', 'controller' => 'import', 'action' => 'getuser'), 'admin_default', true) ?>', {
            'postVar' : 'text',
            'customChoices' : true,
            'minLength': 1,
            'selectMode': 'pick',
            'autocompleteType': 'tag',
            'className': 'tag-autosuggest',
            'filterSubset' : true,
            'multiple' : false,
            'injectChoice': function(token) {
                var choice = new Element('li', {'class': 'autocompleter-choices', 'html': token.photo, 'id':token.label});
                new Element('div', {'html': this.markQueryValue(token.label),'class': 'autocompleter-choice'}).inject(choice);
                this.addChoiceEvents(choice).inject(this.choices);
                choice.store('autocompleteChoice', token);
            }
        });

        pageAutocomplete.addEvent('onSelection', function(element, selected, value, input) {
            $('user_id').value = selected.retrieve('autocompleteChoice').id;
        });

    });
</script>

<?php if( count($this->navigation) ): ?>
<div class='page_admin_tabs'>
  <?php
  echo $this->navigation()->menu()->setContainer($this->navigation)->render();
  ?>
</div>
<?php endif; ?>

<?php if( count($this->sub_navigation) ): ?>
<div class="admin_home_right">
    <ul class="admin_home_dashboard_links">
        <li style="width:200px">
            <ul >
              <?php foreach($this->sub_navigation as $item):?>
                <li class="<?php echo $item->getClass(); ?> hecore-menu-tab <?php if($item->isActive()): ?>active-menu-tab<?php endif; ?>">
                    <a href="<?php echo $item->getHref() ?>">
                      <?php echo $this->translate($item->getLabel()); ?>
                    </a>
                </li>
              <?php endforeach; ?>
            </ul>
        </li>
    </ul>
</div>
<?php endif; ?>

<div class="settings admin_home_middle" style="clear: none;">
  <?php
  echo $this->form->render($this);
  ?>
</div>

<script type="text/javascript">
    window.addEvent('domready', function(){
        if ($('submit_btn')) {
            $('submit_btn').addEvent('click', function(){
                if ( !$('user_id').value ) {
                    alert('Username is Required');
                } else {

                    $('import_form').submit();
                }
            });
        }
    });
</script>